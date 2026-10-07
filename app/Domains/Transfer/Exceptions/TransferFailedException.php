<?php

namespace App\Domains\Transfer\Exceptions;

use Exception;

class TransferFailedException extends Exception
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