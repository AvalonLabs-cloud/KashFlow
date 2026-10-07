<?php

namespace App\Contracts;

use App\DataFactory\FlutterwaveApiRequest as Flutterwave;

interface BIllTransaction
{
    public function verifyAmount(string $biller_code, string $item_code, Flutterwave $flutterwave);

    public function preTransactionProcedure();

    public function exceuteTransaction();
}
