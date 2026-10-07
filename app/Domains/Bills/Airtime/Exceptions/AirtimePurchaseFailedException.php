<?php
namespace App\Domains\Bills\Airtime\Exceptions;

use Exception;

class AirtimePurchaseFailedException extends Exception
{
    public function __construct(
        string $message,
        public readonly array $providerResponse = [],
        public readonly ?string $transactionReference = null,
        public readonly ?string $providerReference = null,
    ) {
        parent::__construct($message);
    }
}
