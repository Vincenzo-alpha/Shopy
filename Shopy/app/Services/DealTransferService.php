<?php

namespace App\Services;

use App\Models\DealArchive;
use App\Models\DealMaster;
use App\Models\DealCompletion;
use App\Models\SellerWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class DealTransferService
{
    /**
     * Generate a cryptographically secure random completion key for a deal.
     * Returns the plaintext key (to show to customer only). Stores only the SHA-256 hash.
     */
    public function generateCompletionKey(int $dealId): string
    {
        $plainKey = 'SHP-' . strtoupper(Str::random(6));
        $hash = hash('sha256', $plainKey);

        DealCompletion::updateOrCreate(
            ['deal_id_fk' => $dealId],
            [
                'completion_key' => $plainKey,
                'completion_key_hash' => $hash,
                'completion_status' => 'pending',
                'completed_at' => null,
            ]
        );

        return $plainKey;
    }

    /**
     * Verify the completion key provided by the customer to the seller,
     * and execute the atomic transfer from sk_deal_archive to sk_deal_master.
     */
    public function completeDealWithKey(int $dealId, string $submittedKey, int $sellerId): array
    {
        $submittedHash = hash('sha256', trim($submittedKey));

        return DB::transaction(function () use ($dealId, $submittedHash, $sellerId) {
            // 1. Lock archive deal
            $deal = DealArchive::where('deal_id_pk', $dealId)
                ->where('seller_id_fk', $sellerId)
                ->lockForUpdate()
                ->first();

            if (!$deal) {
                throw new Exception('Deal not found in active records or unauthorized.');
            }

            if ($deal->payment_status !== 'paid') {
                throw new Exception('Cannot complete deal: payment has not been received.');
            }

            // 2. Check completion key hash
            $completion = DealCompletion::where('deal_id_fk', $dealId)
                ->lockForUpdate()
                ->first();

            if (!$completion) {
                throw new Exception('No completion record exists for this deal.');
            }

            if ($completion->completion_status === 'completed') {
                throw new Exception('This deal has already been marked as completed.');
            }

            if (!hash_equals($completion->completion_key_hash, $submittedHash)) {
                throw new Exception('Invalid completion key. Please check the code provided by the customer.');
            }

            // 3. Insert into sk_deal_master
            $masterDeal = DealMaster::create([
                'deal_unique_no' => $deal->deal_unique_no,
                'interest_id_fk' => $deal->interest_id_fk,
                'seller_id_fk' => $deal->seller_id_fk,
                'customer_id_fk' => $deal->customer_id_fk,
                'prod_service_id_fk' => $deal->prod_service_id_fk,
                'active_status' => 'active',
                'neg_id_fk' => $deal->neg_id_fk,
                'agreed_amount' => $deal->agreed_amount,
                'platform_fee_percent' => $deal->platform_fee_percent,
                'platform_fee' => $deal->platform_fee,
                'seller_net_amount' => $deal->seller_net_amount,
                'fulfillment_method' => $deal->fulfillment_method,
                'payment_status' => 'paid',
                'deal_status' => 'completed',
                'completed_at' => now(),
            ]);

            // 4. Mark completion record
            $completion->update([
                'completion_status' => 'completed',
                'completed_at' => now(),
            ]);

            // 5. Update Seller Wallet: move net earnings from pending_balance to available_balance
            $wallet = SellerWallet::firstOrCreate(
                ['seller_id_fk' => $deal->seller_id_fk],
                ['available_balance' => 0.00, 'pending_balance' => 0.00]
            );

            $netAmount = (float) $deal->seller_net_amount;
            $newPending = max(0.00, (float) $wallet->pending_balance - $netAmount);
            $newAvailable = (float) $wallet->available_balance + $netAmount;

            $wallet->update([
                'pending_balance' => $newPending,
                'available_balance' => $newAvailable,
            ]);

            // 6. Record in wallet transactions ledger
            WalletTransaction::create([
                'seller_id_fk' => $deal->seller_id_fk,
                'deal_id_fk' => $masterDeal->deal_id_pk,
                'transaction_type' => 'credit_earnings',
                'amount' => $netAmount,
                'transaction_status' => 'completed',
                'reference_no' => 'TXN-' . strtoupper(Str::random(10)),
            ]);

            // 7. Delete from sk_deal_archive
            $deal->delete();

            return [
                'success' => true,
                'message' => 'Deal successfully verified and marked as completed!',
                'master_deal' => $masterDeal,
            ];
        });
    }
}
