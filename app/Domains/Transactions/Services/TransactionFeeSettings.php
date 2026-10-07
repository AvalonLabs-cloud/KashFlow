<?php

namespace App\Domains\Transactions\Services;

use App\Models\TransactionFeeSetting;

class TransactionFeeSettings
{
    public function getCurrentPercentage(): float
    {
        $setting = TransactionFeeSetting::where('is_active', true)->first();
        
        return $setting ? (float) $setting->percentage : 0.0;
    }

    public function calculateFee(int $amount): int
    {
        $percentage = $this->getCurrentPercentage();

        if ($percentage <= 0) {
            return 0;
        }

        // Use integer basis-point math to avoid floating point errors with money.
        // E.g., 1.50% becomes 150 basis points.
        $basisPoints = (int) round($percentage * 100);

        // amount * basis_points / 10000
        return (int) intdiv($amount * $basisPoints, 10000);
    }
}