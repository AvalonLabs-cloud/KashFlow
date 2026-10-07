<?php

namespace App\Utills;

use App\DataFactory\FlutterwaveApiRequest as Flutterwave;
use App\Models\User;

class Helper
{
    public function getAvailableBalance(User $user): int
    {
        $transactions = $user->transactions()
            ->where('status', 'completed')
            ->get();

        $credit = $transactions
            ->where('type', 'credit')
            ->sum('amount');

        $debit = $transactions
            ->where('type', 'debit')
            ->sum('amount');

        $heldBalance = $user->heldBalances()
            ->where('status', 'active')
            ->sum('amount');

        return $credit - ($debit + $heldBalance);
    }

    public function verifyBillerAndItemCode(string $billerId, string $itemCode)
    {
        $billerPackageList = app(Flutterwave::class)->get('/billers/'.$billerId.'/items');
        if ($billerPackageList['status'] == 'success') {
            $item = collect($billerPackageList['data'])->where('item_code', $itemCode)->first();
            if (! $item) {
                return [
                    'status' => false,
                    'message' => 'Invalid item code for this biller',
                ];
            }

            return [
                'status' => true,
                'data' => $item,
            ];
        }

        return [
            'status' => false,
            'message' => 'Error fetching data from provider',
        ];
    }
}
