<?php

namespace App\Domains\History\Services;

use App\Models\LedgerEntry;
use App\Models\TransactionHistory;

class CreateHistoryRecord
{
    public function __construct(
    public TransactionHistory $transactionHistory,
) {}

    public function execute(LedgerEntry $ledgerEntry)
    {
        $transactionRelatedToLedger = $ledgerEntry->transaction()->get();

        $data = [
            'user_id' => auth('web')->id(),
            'tranaction_id' => $ledgerEntry->transaction_id,
            'reference' => $ledgerEntry->reference,
            'type' => $ledgerEntry->type,
            'direction' => $ledgerEntry->direction,
            'status' => $transactionRelatedToLedger->status,
            'amount' => $ledgerEntry->amount,
            'recipient_phone' => $ledgerEntry->type === 'AIRTIME' || $ledgerEntry->type === 'DATA' ? $transactionRelatedToLedger->metadata['phoneNumber'] : null,
            'recipient_name' => $ledgerEntry->type === 'TRANSFER' ? $transactionRelatedToLedger->metadata['recipientname'] : null,
            'recipient_account' => $ledgerEntry->type === 'TRANSFER' ? $transactionRelatedToLedger->metadata['accountNumber'] : null,
            'description' => $ledgerEntry->description,
            'recipient_bank_name' => $ledgerEntry->type === 'TRANSFER' ? $transactionRelatedToLedger->metadata['bankCode'] : null,
            'recipient_network' => $ledgerEntry->type === 'AIRTIME' || $ledgerEntry->type ==='DATA' ? $transactionRelatedToLedger->metadata['selctedProvider'] : null,
        ];

        return $this->transactionHistory->create($data);
    }
}
