<?php

namespace App\Console\Commands;

use App\TransactionStatus;
use App\Events\TransactionReconsilationEvent;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransactionReconsilation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:transaction-reconsilation';

    /**
     * The console command description.
     */
    protected $description = 'Resolve pending transactions that have been pending for more than 2 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Transaction::query()
            ->where('status', TransactionStatus::PENDING)
            ->where('created_at', '<=', now()->subMinutes(2))
            ->where(function ($query) {
                $query
                    ->whereNull('next_reconciliation')
                    ->orWhere('next_reconciliation', '<=', now());
            })
            ->lazyById(100)
            ->each(function (Transaction $transaction) {
                $claimed = DB::transaction(function () use ($transaction) {

                    $transaction = Transaction::query()
                        ->whereKey($transaction->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$transaction) {
                        return false;
                    }

                    if (
                        $transaction->status !== TransactionStatus::PENDING ||
                        (
                            $transaction->next_reconciliation !== null &&
                            $transaction->next_reconciliation->isFuture()
                        )
                    ) {
                        return false;
                    }

                    $attempt = $transaction->reconciliation_attempt_count ?? 0;

                    /*
                     * Exponential backoff:
                     *
                     * attempt 0 → 1 minute
                     * attempt 1 → 2 minutes
                     * attempt 2 → 4 minutes
                     * attempt 3 → 8 minutes
                     * attempt 4 → 16 minutes
                     * attempt 5 → 32 minutes
                     * attempt 6+ → 60 minutes
                     */
                    $delayMinutes = min(
                        60,
                        2 ** $attempt
                    );

                    $transaction->update([
                        'next_reconciliation' => now()->addMinutes($delayMinutes),
                        'reconciliation_attempt_count' => $attempt + 1,
                    ]);

                    return true;
                });

                /*
                 * Only dispatch if THIS process successfully claimed
                 * the transaction.
                 */
                if ($claimed) {
                    TransactionReconsilationEvent::dispatch(
                        $transaction->id
                    );
                }
            });

        return self::SUCCESS;
    }
}
