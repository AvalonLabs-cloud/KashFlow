<?php
namespace App\Domains\Transfer\Services;
use  App\ClientProvider\FlutterwaveClient;
use App\Domains\Transfer\Exceptions\TransferFailedException;
use Illuminate\Support\Facades\Log;

class ExcecuteTransaction
{
    public function __construct(public FlutterwaveClient $flutterwaveClient)
    {}

    public function excecute(array $data)
    {
        $result = $this->flutterwaveClient->executeTransfer(
           data: $data
        );
        if ($result['data']['status'] !== 'NEW') {
            throw new TransferFailedException(
                message: "Transfer failed for {$result['data']['reference']}.",
                providerResponse: $result,
                transactionReference: $result['data']['reference'],
                providerReference: $result['data']['meta']['provider_operation_reference'] ?? null,
            );
        }
    }
}
