<?php

namespace App\Domains\Transactions\Admin\Exceptions;

use Exception;

class CashoutFailedException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?string $transactionReference = null,
    ) {
        parent::__construct($message);
    }
}
