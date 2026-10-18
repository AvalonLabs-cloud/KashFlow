<?php

namespace App\Listeners;

use App\Events\DemoCompletion;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\TransactionStatus;

class DemoCompletionListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DemoCompletion $event): void
    {
        sleep(3);
        $transaction = Transaction::where('transaction_reference', $event->tx_ref)->first();
        $transaction->update([
            'status' => TransactionStatus::SUCCESSFUL,
            'completed_at' => now(),
        ]);
    }
}
