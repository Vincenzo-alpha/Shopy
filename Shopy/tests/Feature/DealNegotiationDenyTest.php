<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\DealArchive;
use App\Models\Negotiation;
use App\Models\NegotiationHistory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DealNegotiationDenyTest extends TestCase
{
    use DatabaseTransactions;

    protected Seller $seller;
    protected Customer $customer;
    protected ProductService $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = Seller::create([
            'seller_unique_no' => 'SEL-' . strtoupper(Str::random(6)),
            'seller_name' => 'Deny Test Seller',
            'email' => 'denyseller_' . Str::random(5) . '@shopy.com',
            'password' => Hash::make('password123'),
            'contact_no' => '9876543210',
            'address' => '123 Market St',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'account_status' => 'active',
        ]);

        $this->customer = Customer::create([
            'customer_unique_no' => 'CUS-' . strtoupper(Str::random(6)),
            'customer_name' => 'Deny Test Customer',
            'email' => 'denycustomer_' . Str::random(5) . '@shopy.com',
            'password' => Hash::make('password123'),
            'contact_no' => '9123456780',
            'address' => '456 Customer Ave',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'account_status' => 'active',
        ]);

        $this->product = ProductService::create([
            'prod_servics_unique_no' => 'PRD-' . strtoupper(Str::random(6)),
            'seller_id_fk' => $this->seller->seller_id_pk,
            'prod_service_name' => 'Deny Test Product',
            'item_type' => 'product',
            'category' => 'Electronics',
            'listed_price' => 1000.00,
            'minimum_rate' => 500.00,
            'maximum_rate' => 1500.00,
            'availability_status' => 'available',
            'description' => 'Test item for deny negotiation test',
        ]);
    }

    protected function createNegotiatingDeal(): DealArchive
    {
        $deal = DealArchive::create([
            'deal_unique_no' => 'DL-TEST-' . strtoupper(Str::random(6)),
            'seller_id_fk' => $this->seller->seller_id_pk,
            'customer_id_fk' => $this->customer->customer_id_pk,
            'prod_service_id_fk' => $this->product->prod_service_id_pk,
            'active_status' => 'active',
            'fulfillment_method' => 'customer_collection',
            'payment_status' => 'unpaid',
            'deal_status' => 'negotiating',
        ]);

        $negotiation = Negotiation::create([
            'deal_id_fk' => $deal->deal_id_pk,
            'seller_negotiation_amt' => 1200.00,
            'customer_negotiation_amt' => 900.00,
            'current_offer_by' => 'seller',
            'negotiation_status' => 'in_progress',
        ]);

        $deal->update(['neg_id_fk' => $negotiation->neg_id_pk]);

        NegotiationHistory::create([
            'neg_id_fk' => $negotiation->neg_id_pk,
            'offered_by' => 'seller',
            'amount' => 1200.00,
            'notes' => 'Starting offer',
        ]);

        return $deal;
    }

    public function test_customer_can_deny_seller_offer(): void
    {
        $deal = $this->createNegotiatingDeal();

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.deny', $deal->deal_id_pk), [
                'reason' => 'Price is above my budget',
            ]);

        $response->assertRedirect(route('customer.deals.show', $deal->deal_id_pk));
        $response->assertSessionHas('info');

        $deal->refresh();
        $this->assertEquals('cancelled', $deal->deal_status);
        $this->assertEquals('rejected', $deal->negotiation->negotiation_status);

        $latestHistory = NegotiationHistory::where('neg_id_fk', $deal->negotiation->neg_id_pk)
            ->latest('history_id_pk')
            ->first();

        $this->assertNotNull($latestHistory);
        $this->assertEquals('customer', $latestHistory->offered_by);
        $this->assertStringContainsString('Price is above my budget', $latestHistory->notes);
    }

    public function test_seller_can_deny_customer_counteroffer(): void
    {
        $deal = $this->createNegotiatingDeal();

        $response = $this->actingAs($this->seller, 'seller')
            ->post(route('seller.deals.deny', $deal->deal_id_pk), [
                'reason' => 'Cannot sell below minimum margin',
            ]);

        $response->assertRedirect(route('seller.deals.show', $deal->deal_id_pk));
        $response->assertSessionHas('info');

        $deal->refresh();
        $this->assertEquals('cancelled', $deal->deal_status);
        $this->assertEquals('rejected', $deal->negotiation->negotiation_status);

        $latestHistory = NegotiationHistory::where('neg_id_fk', $deal->negotiation->neg_id_pk)
            ->latest('history_id_pk')
            ->first();

        $this->assertNotNull($latestHistory);
        $this->assertEquals('seller', $latestHistory->offered_by);
        $this->assertStringContainsString('Cannot sell below minimum margin', $latestHistory->notes);
    }

    public function test_cannot_deny_an_already_cancelled_deal(): void
    {
        $deal = $this->createNegotiatingDeal();
        $deal->update(['deal_status' => 'cancelled']);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.deny', $deal->deal_id_pk));

        $response->assertSessionHas('error', 'Deal is no longer in negotiation state.');
    }

    public function test_cannot_accept_offer_on_cancelled_deal(): void
    {
        $deal = $this->createNegotiatingDeal();
        $deal->update(['deal_status' => 'cancelled']);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.accept', $deal->deal_id_pk));

        $response->assertSessionHas('error', 'Deal is no longer in negotiation state.');
    }

    public function test_customer_can_deny_offer_without_reason(): void
    {
        $deal = $this->createNegotiatingDeal();

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('customer.deals.deny', $deal->deal_id_pk));

        $response->assertRedirect(route('customer.deals.show', $deal->deal_id_pk));
        $response->assertSessionHas('info');

        $deal->refresh();
        $this->assertEquals('cancelled', $deal->deal_status);
        $this->assertEquals('rejected', $deal->negotiation->negotiation_status);

        $latestHistory = NegotiationHistory::where('neg_id_fk', $deal->negotiation->neg_id_pk)
            ->latest('history_id_pk')
            ->first();

        $this->assertNotNull($latestHistory);
        $this->assertEquals('Customer denied the offer.', $latestHistory->notes);
    }
}
