<?php
namespace App\Domains\Bills\Data\Services;
use App\ClientProvider\FlutterwaveClient;
use App\Domains\Bills\Data\Exceptions\DataPurchaseFailedException;

class ExcecuteTransaction
{
    public function __construct(
        public FlutterwaveClient $flutterwaveClient,
    ) {}

    public function excecute(array $data)
    {
        $result = $this->flutterwaveClient->executeBillPurchase($data);

        if ($result['status'] !== 'success') {
            throw new DataPurchaseFailedException(
                message: "Airtime purchase failed for {$result['data']['reference']}.",
                providerResponse: $result,
                transactionReference: $result['data']['reference'],
                providerReference: $result['data']['meta']['provider_operation_reference'] ?? null,
            );
        }

        return $result;
    }
}
