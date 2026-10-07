<?php

namespace App\Observers;

use App\Models\LedgerEntry;
use App\Domains\History\Services\CreateHistoryRecord;
use App\Domains\Beneficiary\Services\CheckIfToCreateBeneficiary;
use App\Domains\Beneficiary\Services\CreateBeneficiaryRecord;

class LedgerEntryObserver
{
    public function __construct(
        public CheckIfToCreateBeneficiary $checkIfToCreateBeneficiary,
        public CreateBeneficiaryRecord $createBeneficiaryRecord,
        public CreateHistoryRecord $createHistoryRecord,
    ) {}
    /**
     * Handle the LedgerEntry "created" event.
     */
    public function created(LedgerEntry $ledgerEntry): void
    {
        $historyRecord = $this->createHistoryRecord->execute($ledgerEntry);
        $requiresBeneficiary = $this->checkIfToCreateBeneficiary->execute($historyRecord);
        if ($requiresBeneficiary) {
            $this->createBeneficiaryRecord->execute($historyRecord);
        }
    }

    /**
     * Handle the LedgerEntry "updated" event.
     */
    public function updated(LedgerEntry $ledgerEntry): void
    {
        //
    }

    /**
     * Handle the LedgerEntry "deleted" event.
     */
    public function deleted(LedgerEntry $ledgerEntry): void
    {
        //
    }

    /**
     * Handle the LedgerEntry "restored" event.
     */
    public function restored(LedgerEntry $ledgerEntry): void
    {
        //
    }

    /**
     * Handle the LedgerEntry "force deleted" event.
     */
    public function forceDeleted(LedgerEntry $ledgerEntry): void
    {
        //
    }
}
