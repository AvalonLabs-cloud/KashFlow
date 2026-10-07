<?php

namespace App\Transactions;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Facades\Log;

class RetrieveTransactionFee
{
    public function __construct(
        protected FlutterwaveClient $flutterwaveClient
    ) {}
    public function execute(mixed $amount)
    {
        $result = $this->flutterwaveClient->transactionFee(amount: $amount);
        Log::info('fee' , $result);
        return $result['data']['fee'];
    }
}
