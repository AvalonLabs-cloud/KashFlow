<?php

namespace App\Http\Middleware\Services;

use App\Http\Middleware\Services\ShapeVerifiers\VerifyTransferMoneyDataShape;
use App\Http\Middleware\Services\ShapeVerifiers\VerifyAirtimeShape;
use App\Http\Middleware\Services\ShapeVerifiers\VerifyDataBIllShape;

class DataShapeVerificationService
{

    public function __construct(
        private VerifyTransferMoneyDataShape $verifyTransferMoneyDataShape,
        private VerifyAirtimeShape $verifyAirtimeShape,
        private VerifyDataBIllShape $verifyDataBillShape,
    ) {}

    /**
     * Verify request data according to its transaction type.
     */
    public function verifyDataShape(string $dataType, array $data): void
    {
        switch ($dataType) {
            case 'transfer':
                $this->verifyTransferMoneyDataShape->execute($data);
                break;

            case 'airtime':
              $this->verifyAirtimeShape->execute($data);
              break;

            case 'data':
                $this->verifyDataBillShape->execute($data);
                break;
        }
    }
}
