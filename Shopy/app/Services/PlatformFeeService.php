<?php

namespace App\Services;

use App\Models\PlatformSetting;

class PlatformFeeService
{
    /**
     * Get the active platform fee percentage.
     */
    public function getActiveFeePercentage(): float
    {
        $val = PlatformSetting::getVal('platform_fee_percent', '5.00');
        return (float) $val;
    }

    /**
     * Calculate platform fee and seller net amount for an agreed deal amount.
     *
     * @param float $agreedAmount
     * @param float|null $feePercent Override percent or pull from settings
     * @return array{agreed_amount: float, fee_percent: float, platform_fee: float, seller_net_amount: float}
     */
    public function calculate(float $agreedAmount, ?float $feePercent = null): array
    {
        $percent = $feePercent ?? $this->getActiveFeePercentage();
        $fee = round(($agreedAmount * $percent) / 100, 2);
        $net = round($agreedAmount - $fee, 2);

        return [
            'agreed_amount' => round($agreedAmount, 2),
            'fee_percent' => $percent,
            'platform_fee' => $fee,
            'seller_net_amount' => $net,
        ];
    }
}
