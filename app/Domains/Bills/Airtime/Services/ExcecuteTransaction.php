<?php

namespace App\Domains\Bills\Airtime\Services;

use App\ClientProvider\FlutterwaveClient;
use App\Domains\Transactions\Services\RetrieveBillerAndItemCodeForBills as RetrieveItemAndBillerCode;
use App\Domains\Bills\Airtime\Exceptions\AirtimePurchaseFailedException;

class ExcecuteTransaction
{
    public function __construct(
        public RetrieveItemAndBillerCode $retrieveItemCode,
        public FlutterwaveClient $flutterwaveClient,
    ) {}

    public function excecute(array $data)
    {
        $item_code = $this->retrieveItemCode->execute(billType: $data['bill_type'], billCode: $data['biller_code']);
        $data['item_code'] = $item_code['item_code'];
        $data['biller_code'] = $item_code['biller_code'];
        $result = $this->flutterwaveClient->executeBillPurchase($data);
        if ($result['status'] !== 'success') {
            throw new AirtimePurchaseFailedException(
                message: "Airtime purchase failed for {$result['data']['reference']}.",
                providerResponse: $result,
                transactionReference: $result['data']['reference'],
                providerReference: $result['data']['meta']['provider_operation_reference'] ?? null,
            );
        }
        return $result;
    }
}
