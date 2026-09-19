<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DealArchive;
use App\Models\DealMaster;
use App\Models\Negotiation;
use App\Models\NegotiationHistory;
use App\Models\Payment;
use App\Models\SellerWallet;
use App\Services\DealTransferService;
use App\Services\PlatformFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerDealController
{
    public function __construct(
        protected PlatformFeeService $feeService,
        protected DealTransferService $transferService
    ) {}

    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $activeDeals = DealArchive::with(['seller', 'product', 'negotiation', 'completion'])
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.deals.index', compact('activeDeals'));
    }

    public function show($id)
    {
        $customer = Auth::guard('customer')->user();
        $deal = DealArchive::with(['seller', 'product', 'negotiation.history', 'completion', 'payments'])
            ->where('deal_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        $feePreview = null;
        if ($deal->negotiation && $deal->negotiation->seller_negotiation_amt > 0) {
            $feePreview = $this->feeService->calculate((float) $deal->negotiation->seller_negotiation_amt);
        }

        return view('customer.deals.show', compact('deal', 'feePreview'));
    }

    public function counterOffer(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $deal = DealArchive::with(['product', 'negotiation'])
            ->where('deal_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'negotiating') {
            return back()->with('error', 'Negotiation is locked for this deal.');
        }

        $min = (float) $deal->product->minimum_rate;
        $max = (float) $deal->product->maximum_rate;

        $request->validate([
            'offer_amount' => "required|numeric|min:{$min}|max:{$max}",
            'notes' => 'nullable|string|max:255',
        ], [
            'offer_amount.min' => "Offer must be at least ₹{$min} (Seller minimum acceptable rate).",
            'offer_amount.max' => "Offer cannot exceed ₹{$max} (Seller maximum rate).",
        ]);

        $amount = round((float) $request->offer_amount, 2);

        DB::transaction(function () use ($deal, $amount, $request) {
            $deal->negotiation->update([
                'customer_negotiation_amt' => $amount,
                'current_offer_by' => 'customer',
            ]);

            NegotiationHistory::create([
                'neg_id_fk' => $deal->negotiation->neg_id_pk,
                'offered_by' => 'customer',
                'amount' => $amount,
                'notes' => $request->notes ?? 'Customer submitted counteroffer',
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Counteroffer submitted successfully!',
                'amount' => $amount,
            ]);
        }

        return back()->with('success', 'Your counteroffer of ₹' . number_format($amount, 2) . ' has been submitted.');
    }

    public function acceptOffer(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $deal = DealArchive::with(['product', 'negotiation'])
            ->where('deal_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'negotiating') {
            return back()->with('error', 'Deal is no longer in negotiation state.');
        }

        // Customer accepts the seller's current offer
        $agreedAmount = (float) $deal->negotiation->seller_negotiation_amt;
        $feeCalculation = $this->feeService->calculate($agreedAmount);

        DB::transaction(function () use ($deal, $agreedAmount, $feeCalculation) {
            $deal->update([
                'agreed_amount' => $agreedAmount,
                'platform_fee_percent' => $feeCalculation['fee_percent'],
                'platform_fee' => $feeCalculation['platform_fee'],
                'seller_net_amount' => $feeCalculation['seller_net_amount'],
                'deal_status' => 'payment_pending',
                'payment_status' => 'unpaid',
            ]);

            $deal->negotiation->update([
                'negotiation_status' => 'accepted',
            ]);

            NegotiationHistory::create([
                'neg_id_fk' => $deal->negotiation->neg_id_pk,
                'offered_by' => 'customer',
                'amount' => $agreedAmount,
                'notes' => 'Customer accepted seller offer of ₹' . number_format($agreedAmount, 2),
            ]);
        });

        return redirect()->route('customer.deals.payment', $deal->deal_id_pk)
            ->with('success', 'Offer accepted! Please proceed to complete the sandbox payment.');
    }

    public function paymentScreen($id)
    {
        $customer = Auth::guard('customer')->user();
        $deal = DealArchive::with(['seller', 'product'])
            ->where('deal_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'payment_pending' && $deal->deal_status !== 'negotiating') {
            if ($deal->payment_status === 'paid') {
                return redirect()->route('customer.deals.show', $deal->deal_id_pk)
                    ->with('info', 'Payment for this deal has already been received.');
            }
        }

        return view('customer.deals.payment', compact('deal'));
    }

    public function processPayment(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $deal = DealArchive::with(['seller', 'product'])
            ->where('deal_id_pk', $id)
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->firstOrFail();

        $request->validate([
            'payment_outcome' => 'required|in:success,failed',
            'payment_method' => 'required|in:sandbox_card,sandbox_upi,sandbox_netbanking',
        ]);

        $reference = 'SANDBOX_PAY_' . strtoupper(Str::random(12));
        $outcome = $request->payment_outcome;

        if ($outcome === 'success') {
            $plainCompletionKey = null;

            DB::transaction(function () use ($deal, $customer, $reference, $request, &$plainCompletionKey) {
                // 1. Record payment
                Payment::create([
                    'deal_id_fk' => $deal->deal_id_pk,
                    'customer_id_fk' => $customer->customer_id_pk,
                    'amount' => $deal->agreed_amount,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'success',
                    'payment_reference' => $reference,
                    'paid_at' => now(),
                ]);

                // 2. Update Deal status
                $deal->update([
                    'payment_status' => 'paid',
                    'deal_status' => 'in_fulfillment',
                ]);

                // 3. Generate Cryptographic Deal Completion Key (Stores hash only)
                $plainCompletionKey = $this->transferService->generateCompletionKey($deal->deal_id_pk);

                // 4. Update Seller Wallet: Add to pending_balance (NOT available_balance until completed)
                $wallet = SellerWallet::firstOrCreate(
                    ['seller_id_fk' => $deal->seller_id_fk],
                    ['available_balance' => 0.00, 'pending_balance' => 0.00]
                );

                $wallet->increment('pending_balance', (float) $deal->seller_net_amount);
            });

            // Store plain key in session flash so customer can note it
            session()->flash('completion_key_revealed', $plainCompletionKey);

            return redirect()->route('customer.deals.show', $deal->deal_id_pk)
                ->with('success', 'Sandbox payment simulated successfully! Your completion key has been generated below.');
        } else {
            // Simulated failure
            Payment::create([
                'deal_id_fk' => $deal->deal_id_pk,
                'customer_id_fk' => $customer->customer_id_pk,
                'amount' => $deal->agreed_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'failed',
                'payment_reference' => $reference,
                'paid_at' => null,
            ]);

            $deal->update([
                'payment_status' => 'failed',
            ]);

            return back()->with('error', 'Simulated payment failed as requested. You may retry anytime.');
        }
    }

    public function completedDeals()
    {
        $customer = Auth::guard('customer')->user();
        $completedDeals = DealMaster::with(['seller', 'product', 'completion'])
            ->where('customer_id_fk', $customer->customer_id_pk)
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        return view('customer.deals.completed', compact('completedDeals'));
    }
}
