<?php

namespace App\Domains\Reconsilation\Services;

use App\AdminTransactionStatus;
use App\TransactionStatus;
use App\HoldStatus;
use Illuminate\Support\Facades\DB;
use App\Models\LedgerEntry;
use App\Notifications\MoneyMovementAlert;
use App\TransactionReconsilationStatus;


class markReconsilationFailure
{
    public function execute(mixed $reconsilation)
    {
        return  DB::transaction(function () use (
            $reconsilation
        ) {

            if ($reconsilation->status !== TransactionReconsilationStatus::Pending) {
                return;
            }


            $transaction = $reconsilation->transaction()->lockForUpdate()->first();
            if (!$transaction) {
                return;
            }

            if ($transaction->status === TransactionStatus::FAILED) {
                $reconsilation->update([
                    'status' => TransactionReconsilationStatus::Failed,
                    'reconsilation_time' => now(),
                ]);
                return;
            }


            $transaction->adminTransaction->update([
                'status' => AdminTransactionStatus::Failed
            ]);

            $ledgerEntry = LedgerEntry::query()
                ->where(
                    'reference',
                    $transaction->reference
                )
                ->lockForUpdate()
                ->first();
            $account = $transaction->user->account()->lockForUpdate()->first();

            if (!$ledgerEntry) {
                LedgerEntry::create([
                    'account_id' => $account->id,
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'type' => $transaction->type,
                    'direction' => $transaction->direction,
                    'amount' => $transaction->amount,
                    'description' =>
                    'Transaction failed',
                ]);

                $transaction->hold()->update([
                    'status' => HoldStatus::Released,
                    'released_at' => now(),
                ]);
            }

            $transaction->update([
                'status' => TransactionStatus::FAILED,
                'completed_at' => now(),
            ]);


            $transaction->user()->notify(new MoneyMovementAlert(clientName: $transaction->user()->full_name, direction: $transaction->direction, amount: $transaction->amount, mode: $transaction->status->value));
        });
    }
}
