<?php

namespace App\Domains\Bills\Data\Services;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

class RetrievePriceOfDataBundle
{
    public function __construct(
        public FlutterwaveClient $flutterwaveClient
    ) {}

    public function execute(mixed $itemCode)
    {
        $billerCode = Context::get('billerCode');

        $billerServices = $this->flutterwaveClient->getItemCodesForBill(
            billerCode: $billerCode
        );

        Log::info('Retrieved biller services', [
            'biller_code' => $billerCode,
            'item_code' => $itemCode,
        ]);

        $billerItem = collect($billerServices['data'])
            ->firstWhere('item_code', $itemCode);

        if (!$billerItem) {
            Log::warning('Biller item not found', [
                'biller_code' => $billerCode,
                'item_code' => $itemCode,
            ]);

            throw new \RuntimeException(
                "Biller item {$itemCode} was not found."
            );
        }

        $billerItemPrice = $billerItem['amount'];
        $billerItemFee = $billerItem['fee'];

        $totalAmountToPay = $billerItemPrice + $billerItemFee;

        Log::info('Biller item price calculated', [
            'item_code' => $itemCode,
            'price' => $billerItemPrice,
            'fee' => $billerItemFee,
            'total_amount' => $totalAmountToPay,
        ]);

        Context::add(
            'data_transaction_amount',
            $totalAmountToPay
        );

        return $totalAmountToPay;
    }
}