<?php

namespace App\Http\Middleware\Services;

use App\Http\Middleware\Services\ShapeValueVerifiers\VerifyTransferMoneyShapeValue;
use App\Http\Middleware\Services\ShapeValueVerifiers\VerifyDataBillShapeValue;
use Illuminate\Support\Facades\Log;

class DataShapeValueVerificationService
{
    public function __construct(
        private VerifyTransferMoneyShapeValue $verifyTransferMoneyDataShapeValue,
        private VerifyDataBillShapeValue $verifyDataBillShapeValue,
    ) {}

    public function verifyDataShapeValue(string $dataType, array $data): void
    {
        Log::info('shape verification hit');
        switch ($dataType) {
            case 'transfer':
                $this->verifyTransferMoneyDataShapeValue->execute($data);
                break;
            case 'airtime':
                break;
            case 'data':
                $this->verifyDataBillShapeValue->execute($data);
                break;
        }
    }
}
