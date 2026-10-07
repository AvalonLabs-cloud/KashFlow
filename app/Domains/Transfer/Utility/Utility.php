<?php
namespace App\Domains\Transfer\Utility;

class Utility
{
    public static function requiredFieldsForTransferMoneyTypeRequest(): array
    {
        return [
            'amount',
            'accountNumber',
            'bankCode',
            'recipientname',
            'transactionType',
        ];
    }

    public static function TransactionTypeEndPointMap (): array
    {
        return [
            'transfer' => '/transaction/transfer',
        ];
    }
}
