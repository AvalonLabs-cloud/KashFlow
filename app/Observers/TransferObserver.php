<?php

namespace App\Observers;

use App\Models\Transfer;
use Illuminate\Support\Str;

class TransferObserver
{
    /**
     * Handle the Tansfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        $makeShiftUnuid = Str::random(16);
        $transfer->updateQuietly(['identity' => $makeShiftUnuid]);
    }

    /**
     * Handle the Tansfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Tansfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Tansfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Tansfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        //
    }
}
