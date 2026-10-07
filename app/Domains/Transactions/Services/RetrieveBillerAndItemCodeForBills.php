<?php

namespace App\Domains\Transactions\Services;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RetrieveBillerAndItemCodeForBills
{
    public function __construct(
        protected Flutterwaveclient $flutterwaveClient,
    ) {}

    public function execute(string $billType, string $billCode)
    {
        $billerAndItemCode = match ($billType) {
            'airtime' => $this->getAirtimeBillerAndItemCode(billType: $billType, billCode: $billCode),
        };

        return $billerAndItemCode;
    }

    public function getAirtimeBillerAndItemCode(mixed $billType, mixed $billCode)
    {
        $airtimeBillProviders = collect($this->flutterwaveClient->getBillProvidersList(billType:  Str::ucfirst($billType) ))->get('data');
        Log::info('bill data ' , $airtimeBillProviders);
        $itemCodeRequestResult = collect($this->flutterwaveClient->getItemCodesForBill(billerCode: $billCode))->get('data');
        $itemCode = collect($itemCodeRequestResult)->where('biller_code', $billCode)->value('item_code');
        return [
            'biller_code' => $billCode,
            'item_code' => $itemCode,
        ];
    }
}
