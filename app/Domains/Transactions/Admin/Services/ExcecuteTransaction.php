<?php
namespace App\Domains\Transactions\Admin\Services;
use  App\ClientProvider\FlutterwaveClient;
use App\Domains\Transactions\Admin\Exceptions\CashoutFailedException;
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
            throw new CashoutFailedException(
                message: "Transfer failed for {$result['data']['reference']}.",
                transactionReference: $result['data']['reference'],
            );
        }

    }
}
