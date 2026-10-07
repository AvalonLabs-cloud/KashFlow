<?php

namespace App\Domains\Beneficiary\Services;

use App\Models\Beneficiary;
use  App\Models\TransactionHistory;

class CreateBeneficiaryRecord
{
    public function __construct(
        public Beneficiary $beneficiary,
    ) {}

    public function execute(TransactionHistory $history)
    {
        $airtimeOrDataBeneficiaryExists = $this->beneficiary->where('type', $history->type)
            ->where('recipient_phone', $history->recipient_phone)
            ->where('recipient_network', $history->recipient_network)
            ->first();
        if ($airtimeOrDataBeneficiaryExists) {
            return;
        }

        $transferBeneficiaryExists = $this->beneficiary->where('type', $history->type)
            ->where('recipient_name', $history->recipient_name)
            ->where('recipient_account', $history->recipient_account)
            ->where('recipient_bank_name', $history->recipient_bank_name)
            ->first();
        if ($transferBeneficiaryExists) {
            return;
        }

        $data = [
            'user_id' => auth('web')->id(),
            'type' => $history->type,
            'recipient_phone' => $history->type === 'AIRTIME' || $history->type === 'DATA' ? $history->recipient_phone : null,
            'recipient_name' => $history->type === 'TRANSFER' ? $history->recipient_name : null,
            'recipient_account' => $history->type === 'TRANSFER' ? $history->recipient_account : null,
            'recipient_bank_name' => $history->type === 'TRANSFER' ? $history->recipient_bank_name : null,
            'recipient_network' => $history->type === 'AIRTIME' || $history->type === 'DATA' ? $history->recipient_network : null,
        ];

        return $this->beneficiary->create($data);
    }
}
