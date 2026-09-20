<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\DealArchive;
use App\Models\DealMaster;
use App\Models\Interest;
use App\Models\Negotiation;
use App\Models\NegotiationHistory;
use App\Models\SellerWallet;
use App\Models\WalletTransaction;
use App\Services\DealTransferService;
use App\Services\PlatformFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class SellerDealController
{
    public function __construct(
        protected PlatformFeeService $feeService,
        protected DealTransferService $transferService
    ) {}

    public function interests()
    {
        $seller = Auth::guard('seller')->user();
        $interests = Interest::with(['customer', 'product', 'deal'])
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.deals.interests', compact('interests'));
    }

    public function initiateDeal(Request $request, $interestId)
    {
        $seller = Auth::guard('seller')->user();
        $interest = Interest::with('product')
            ->where('interest_id_pk', $interestId)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        if ($interest->interest_status === 'Converted to deal') {
            return back()->with('info', 'A deal has already been initiated for this interest.');
        }

        $deal = DB::transaction(function () use ($interest, $seller) {
            $dealUniqueNo = 'DL-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $product = $interest->product;
            $maxRate = (float) $product->maximum_rate;

            $fulfillment = $product->item_type === 'service' ? 'service_receipt' : 'customer_collection';

            // Create Deal in Archive
            $deal = DealArchive::create([
                'deal_unique_no' => $dealUniqueNo,
                'interest_id_fk' => $interest->interest_id_pk,
                'seller_id_fk' => $seller->seller_id_pk,
                'customer_id_fk' => $interest->customer_id_fk,
                'prod_service_id_fk' => $product->prod_service_id_pk,
                'active_status' => 'active',
                'fulfillment_method' => $fulfillment,
                'payment_status' => 'unpaid',
                'deal_status' => 'negotiating',
            ]);

            // Create initial Negotiation: initialize both to seller's maximum rate as specified
            $negotiation = Negotiation::create([
                'deal_id_fk' => $deal->deal_id_pk,
                'seller_negotiation_amt' => $maxRate,
                'customer_negotiation_amt' => $maxRate,
                'current_offer_by' => 'seller',
                'negotiation_status' => 'in_progress',
            ]);

            $deal->update(['neg_id_fk' => $negotiation->neg_id_pk]);

            // Log first offer
            NegotiationHistory::create([
                'neg_id_fk' => $negotiation->neg_id_pk,
                'offered_by' => 'seller',
                'amount' => $maxRate,
                'notes' => 'Deal initiated. Starting offer set to maximum rate: ₹' . number_format($maxRate, 2),
            ]);

            // Update Interest Status
            $interest->update(['interest_status' => 'Converted to deal']);

            return $deal;
        });

        return redirect()->route('seller.deals.show', $deal->deal_id_pk)
            ->with('success', 'Deal created! Negotiation opened with initial offer of ₹' . number_format($deal->product->maximum_rate, 2));
    }

    public function deals()
    {
        $seller = Auth::guard('seller')->user();
        $deals = DealArchive::with(['customer', 'product', 'negotiation', 'completion'])
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.deals.index', compact('deals'));
    }

    public function showDeal($id)
    {
        $seller = Auth::guard('seller')->user();
        $deal = DealArchive::with(['customer', 'product', 'negotiation.history', 'completion', 'payments'])
            ->where('deal_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        $feePreview = null;
        if ($deal->negotiation && $deal->negotiation->customer_negotiation_amt > 0) {
            $feePreview = $this->feeService->calculate((float) $deal->negotiation->customer_negotiation_amt);
        }

        return view('seller.deals.show', compact('deal', 'feePreview'));
    }

    public function counterOffer(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        $deal = DealArchive::with(['product', 'negotiation'])
            ->where('deal_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'negotiating') {
            return back()->with('error', 'Negotiation is frozen for this deal.');
        }

        $min = (float) $deal->product->minimum_rate;
        $max = (float) $deal->product->maximum_rate;

        $request->validate([
            'offer_amount' => "required|numeric|min:{$min}|max:{$max}",
            'notes' => 'nullable|string|max:255',
        ], [
            'offer_amount.min' => "Your offer cannot be lower than your listing minimum rate of ₹{$min}.",
            'offer_amount.max' => "Your offer cannot exceed your listing maximum rate of ₹{$max}.",
        ]);

        $amount = round((float) $request->offer_amount, 2);

        DB::transaction(function () use ($deal, $amount, $request) {
            $deal->negotiation->update([
                'seller_negotiation_amt' => $amount,
                'current_offer_by' => 'seller',
            ]);

            NegotiationHistory::create([
                'neg_id_fk' => $deal->negotiation->neg_id_pk,
                'offered_by' => 'seller',
                'amount' => $amount,
                'notes' => $request->notes ?? 'Seller submitted counteroffer',
            ]);
        });

        return back()->with('success', 'Counteroffer of ₹' . number_format($amount, 2) . ' sent to customer.');
    }

    public function acceptOffer(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        $deal = DealArchive::with(['product', 'negotiation'])
            ->where('deal_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'negotiating') {
            return back()->with('error', 'Deal is no longer in negotiation state.');
        }

        // Seller accepts the customer's current counteroffer
        $agreedAmount = (float) $deal->negotiation->customer_negotiation_amt;
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
                'offered_by' => 'seller',
                'amount' => $agreedAmount,
                'notes' => 'Seller accepted customer offer of ₹' . number_format($agreedAmount, 2),
            ]);
        });

        return back()->with('success', 'Customer offer accepted! Awaiting customer payment of ₹' . number_format($agreedAmount, 2));
    }

    public function denyOffer(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        $deal = DealArchive::with(['product', 'negotiation'])
            ->where('deal_id_pk', $id)
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->firstOrFail();

        if ($deal->deal_status !== 'negotiating') {
            return back()->with('error', 'Deal is no longer in negotiation state.');
        }

        $reason = $request->input('reason');
        $noteText = 'Seller denied the offer.' . ($reason ? ' Reason: ' . $reason : '');

        DB::transaction(function () use ($deal, $noteText) {
            $deal->update([
                'deal_status' => 'cancelled',
            ]);

            if ($deal->interest_id_fk) {
                Interest::where('interest_id_pk', $deal->interest_id_fk)->update([
                    'interest_status' => 'Deal Denied',
                ]);
            }

            if ($deal->negotiation) {
                $deal->negotiation->update([
                    'negotiation_status' => 'rejected',
                ]);

                NegotiationHistory::create([
                    'neg_id_fk' => $deal->negotiation->neg_id_pk,
                    'offered_by' => 'seller',
                    'amount' => (float) ($deal->negotiation->customer_negotiation_amt ?? 0),
                    'notes' => $noteText,
                ]);
            }
        });

        return redirect()->route('seller.deals.show', $deal->deal_id_pk)
            ->with('info', 'Offer has been denied. Deal negotiation has been terminated.');
    }

    public function verifyCompletionKey(Request $request, $id)
    {
        $seller = Auth::guard('seller')->user();
        $request->validate([
            'completion_key' => 'required|string|max:50',
        ]);

        try {
            $result = $this->transferService->completeDealWithKey(
                (int) $id,
                $request->completion_key,
                $seller->seller_id_pk
            );

            return redirect()->route('seller.deals.completed')
                ->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function completedDeals()
    {
        $seller = Auth::guard('seller')->user();
        $completedDeals = DealMaster::with(['customer', 'product', 'completion'])
            ->where('seller_id_fk', $seller->seller_id_pk)
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        return view('seller.deals.completed', compact('completedDeals'));
    }

    public function wallet()
    {
        $seller = Auth::guard('seller')->user();
        $wallet = SellerWallet::firstOrCreate(
            ['seller_id_fk' => $seller->seller_id_pk],
            ['available_balance' => 0.00, 'pending_balance' => 0.00]
        );

        $transactions = WalletTransaction::where('seller_id_fk', $seller->seller_id_pk)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('seller.wallet.index', compact('wallet', 'transactions'));
    }
}
