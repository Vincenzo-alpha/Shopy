<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\Interest;
use App\Models\DealArchive;
use App\Models\DealMaster;
use App\Models\SellerWallet;
use App\Services\DealTransferService;
use App\Services\PlatformFeeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceWorkflowTest extends TestCase
{
    protected Seller $seller;
    protected Customer $customer;
    protected ProductService $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = Seller::firstOrCreate(
            ['email' => 'testseller@shopy.com'],
            [
                'seller_unique_no' => 'SEL-TEST-' . Str::random(4),
                'seller_name' => 'Test Electronics Store',
                'password' => Hash::make('password123'),
                'contact_no' => '9876543210',
                'address' => '123 Market St',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'account_status' => 'active',
            ]
        );

        SellerWallet::firstOrCreate(
            ['seller_id_fk' => $this->seller->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        $this->customer = Customer::firstOrCreate(
            ['email' => 'testcustomer@shopy.com'],
            [
                'customer_unique_no' => 'CUS-TEST-' . Str::random(4),
                'customer_name' => 'Test Customer',
                'password' => Hash::make('password123'),
                'contact_no' => '9123456780',
                'address' => '456 Customer Ave',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'account_status' => 'active',
            ]
        );

        $this->product = ProductService::firstOrCreate(
            ['prod_servics_unique_no' => 'PRD-TEST-001'],
            [
                'seller_id_fk' => $this->seller->seller_id_pk,
                'prod_service_name' => 'Sony Wireless Headphones',
                'description' => 'Test noise canceling headphones',
                'category' => 'Electronics',
                'item_type' => 'product',
                'listed_price' => 1000.00,
                'minimum_rate' => 800.00,
                'maximum_rate' => 1200.00,
                'availability_status' => 'available',
            ]
        );
    }

    public function test_marketplace_homepage_loads_and_displays_listings()
    {
        $response = $this->get(route('marketplace.index'));
        $response->assertStatus(200);
        $response->assertSee('Sony Wireless Headphones');
    }

    public function test_platform_fee_calculation()
    {
        $feeService = app(PlatformFeeService::class);
        $calc = $feeService->calculate(1000.00, 5.00);

        $this->assertEquals(1000.00, $calc['agreed_amount']);
        $this->assertEquals(50.00, $calc['platform_fee']);
        $this->assertEquals(950.00, $calc['seller_net_amount']);
    }

    public function test_customer_can_express_interest_and_duplicate_is_prevented()
    {
        // 1. Express interest
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.interests.express'), [
                'prod_service_id' => $this->product->prod_service_id_pk,
            ]);

        $response->assertRedirect();
        
        $interest = Interest::where('customer_id_fk', $this->customer->customer_id_pk)
            ->where('prod_service_id_fk', $this->product->prod_service_id_pk)
            ->first();

        $this->assertNotNull($interest);
        $this->assertEquals('Active', $interest->interest_status);

        // 2. Duplicate attempt
        $dupResponse = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.interests.express'), [
                'prod_service_id' => $this->product->prod_service_id_pk,
            ]);

        $dupResponse->assertSessionHas('info');
    }

    public function test_seller_initiates_deal_starting_at_seller_maximum_rate()
    {
        $interest = Interest::firstOrCreate([
            'seller_id_fk' => $this->seller->seller_id_pk,
            'customer_id_fk' => $this->customer->customer_id_pk,
            'prod_service_id_fk' => $this->product->prod_service_id_pk,
        ], [
            'interest_unique_no' => 'INT-' . Str::random(6),
            'interest_status' => 'Active',
        ]);

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.deals.initiate', $interest->interest_id_pk));

        $response->assertRedirect();

        $deal = DealArchive::where('seller_id_fk', $this->seller->seller_id_pk)
            ->where('customer_id_fk', $this->customer->customer_id_pk)
            ->latest('deal_id_pk')
            ->first();

        $this->assertNotNull($deal);
        $this->assertEquals('negotiating', $deal->deal_status);
        $this->assertNotNull($deal->negotiation);

        // Requirement: "When a deal is created, initialize the seller's offer using the seller's maximum rate. Initialize customer_negotiation_amt to the seller's maximum rate"
        $this->assertEquals(1200.00, (float) $deal->negotiation->seller_negotiation_amt);
        $this->assertEquals(1200.00, (float) $deal->negotiation->customer_negotiation_amt);
    }

    public function test_negotiation_counteroffer_within_and_outside_bounds()
    {
        $deal = DealArchive::where('seller_id_fk', $this->seller->seller_id_pk)
            ->where('customer_id_fk', $this->customer->customer_id_pk)
            ->latest('deal_id_pk')
            ->first();

        if (!$deal) {
            $this->test_seller_initiates_deal_starting_at_seller_maximum_rate();
            $deal = DealArchive::where('seller_id_fk', $this->seller->seller_id_pk)->latest('deal_id_pk')->first();
        }

        // Test below minimum rate (800) -> should fail validation
        $invalidResponse = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.counter', $deal->deal_id_pk), [
                'offer_amount' => 700.00,
            ]);
        $invalidResponse->assertSessionHasErrors('offer_amount');

        // Test valid offer (950) -> should succeed
        $validResponse = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.counter', $deal->deal_id_pk), [
                'offer_amount' => 950.00,
                'notes' => 'I can pick it up today',
            ]);
        $validResponse->assertRedirect();

        $deal->refresh();
        $this->assertEquals(950.00, (float) $deal->negotiation->customer_negotiation_amt);
        $this->assertEquals('customer', $deal->negotiation->current_offer_by);
    }

    public function test_offer_acceptance_freezes_deal_and_sandbox_payment_and_completion_transfer()
    {
        $deal = DealArchive::where('seller_id_fk', $this->seller->seller_id_pk)
            ->where('customer_id_fk', $this->customer->customer_id_pk)
            ->where('deal_status', 'negotiating')
            ->latest('deal_id_pk')
            ->first();

        // 1. Seller accepts customer's offer of ₹950
        $this->actingAs($this->seller, 'seller')
            ->post(route('seller.deals.accept', $deal->deal_id_pk));

        $deal->refresh();
        $this->assertEquals('payment_pending', $deal->deal_status);
        $this->assertEquals(950.00, (float) $deal->agreed_amount);
        $this->assertEquals(47.50, (float) $deal->platform_fee); // 5% of 950
        $this->assertEquals(902.50, (float) $deal->seller_net_amount);

        // 2. Customer performs sandbox payment (simulate success)
        $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.payment.process', $deal->deal_id_pk), [
                'payment_method' => 'sandbox_card',
                'payment_outcome' => 'success',
            ]);

        $deal->refresh();
        $this->assertEquals('paid', $deal->payment_status);
        $this->assertEquals('in_fulfillment', $deal->deal_status);

        // Check seller wallet pending balance credited
        $wallet = SellerWallet::where('seller_id_fk', $this->seller->seller_id_pk)->first();
        $this->assertEquals(902.50, (float) $wallet->pending_balance);
        $this->assertEquals(0.00, (float) $wallet->available_balance);

        // Retrieve the generated plaintext key by calling transfer service
        $transferService = app(DealTransferService::class);
        $plainKey = $transferService->generateCompletionKey($deal->deal_id_pk);

        // 3. Test wrong key rejected
        $wrongResponse = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.deals.verify', $deal->deal_id_pk), [
                'completion_key' => 'WRONG-KEY',
            ]);
        $wrongResponse->assertSessionHas('error');

        // 4. Test correct key transfers deal from archive to master!
        $correctResponse = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.deals.verify', $deal->deal_id_pk), [
                'completion_key' => $plainKey,
            ]);

        $correctResponse->assertRedirect(route('seller.deals.completed'));

        // Deal archive record should be deleted!
        $this->assertNull(DealArchive::find($deal->deal_id_pk));

        // Deal master record should exist with the SAME deal_unique_no!
        $masterDeal = DealMaster::where('deal_unique_no', $deal->deal_unique_no)->first();
        $this->assertNotNull($masterDeal);
        $this->assertEquals('completed', $masterDeal->deal_status);
        $this->assertNotNull($masterDeal->completed_at);
        $this->assertEquals(902.50, (float) $masterDeal->seller_net_amount);

        // Wallet should have moved from pending to available balance!
        $wallet->refresh();
        $this->assertEquals(0.00, (float) $wallet->pending_balance);
        $this->assertEquals(902.50, (float) $wallet->available_balance);
    }
}
