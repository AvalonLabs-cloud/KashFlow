<?php

namespace App\Traits\BillBaseServiceTrait;

use App\DataFactory\FlutterwaveApiRequest as Flutterwave;
use App\Utills\FlutterwaveApiRoutes\FlutterwaveApiRoutes;
use App\Utills\Helper;

trait BillBaseServiceTrait
{
    public function verifyAmount(string $biller_code, string $item_code)
    {
        $billInformation = app(Flutterwave::class)->get(FlutterwaveApiRoutes::getBillInformationPath($biller_code));
        if ($billInformation['status'] == 'success') {
            $item_cost = collect($billInformation['data'])->where('item_code', $item_code)->first()['amount'];
            $available_balance = app(Helper::class)->getAvailableBalance(auth('web')->user());
            $is_amount_sufficient = $available_balance >= $item_cost;
            if (! $is_amount_sufficient) {
                return [
                    'status' => false,
                    'is_amount_sufficient' => false,
                    'message' => config('messages.insufficient_balance'),
                ];
            }
            $amount_remaining = $available_balance - $item_cost;
            $message = config('messages.sufficient_balance');

            return [
                'status' => true,
                'is_amount_sufficient' => $is_amount_sufficient,
                'amount_remaining' => $amount_remaining,
                'message' => $message,
            ];
        }

        return [
            'status' => false,
            'message' => config('messages.billInformationError'),
        ];
    }

    public function preTransactionProcedure() {}

    public function exceuteTransaction() {}
}
