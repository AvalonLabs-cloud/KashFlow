<?php

namespace App\Http\Middleware\Services\ShapeValueVerifiers;

use App\ClientProvider\FlutterwaveClient;
use InvalidArgumentException;

class VerifyTransferMoneyShapeValue
{
    public function __construct(public FlutterwaveClient $flutterwaveClient) {}
    public function execute(array $data)
    {
        if (app()->environment('production')) {
            $result =  $this->flutterwaveClient->resolveAccount(accountNumber: $data['accountNumber'], accountBank: $data['bankCode']);
            if ($result['status'] !== 'success') {
                throw new InvalidArgumentException(
                    'Invalid transfer request data'
                );
            }
            if ($result['data']['account_name'] !== $data['recipientname']) {
                throw new InvalidArgumentException(
                    'Invalid transfer request data'
                );
            }
        }
    }
}
