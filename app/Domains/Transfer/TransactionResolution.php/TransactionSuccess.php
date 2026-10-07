<?php

namespace App\Domains\Transfer\TransactionResolution;

use App\AdminTransactionStatus;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Models\WebHookEvent;
use App\Models\Transaction;
use App\Models\ProviderOperation;
use App\Models\LedgerEntry;
use App\Notifications\MoneyMovementAlert;

class TransactionSuccess {
    public function execute(mixed $webhook, mixed  $providerTransfer) {
        return  DB::transaction(function () use (
        $webhook,
        $providerTransfer
    ) {

        $webhook = WebHookEvent::query()
            ->whereKey($webhook->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($webhook->processed_at !== null) {
            return;
        }

        $transaction = Transaction::query()
            ->where(
                'reference',
                $webhook->transaction_reference
            )
            ->lockForUpdate()
            ->first();


        if (! $transaction) {
            throw new \RuntimeException(
                'Internal transaction not found: '
                . $webhook->transaction_reference
            );
        }

        if ($transaction->status === 'successful') {

            $webhook->update([
                'processed_at' => now(),
                'processing_error' => null,
            ]);

            return;
        }

        if (
            bccomp(
                (string) $transaction->amount,
                (string) $providerTransfer->amount,
                2
            ) !== 0
        ) {
            throw new \RuntimeException(
                'Transfer amount mismatch.'
            );
        }



        $provider= ProviderOperation::query()
            ->where(
                'client_reference',
                $webhook->provider_reference,
            )
            ->lockForUpdate()
            ->first();

        if (!$provider) {
            throw new \RuntimeException(
                "provider record not found for transaction with id"
                . $transaction->id
            );
        }

        if ($provider->status === 'successful') {

            $webhook->update([
                'processed_at' => now(),
                'processing_error' => null,
            ]);

            return;
        }

        $account = $transaction->account()->lockForUpdate()->first();
        $transaction->adminTransaction->update([
            'status' => AdminTransactionStatus::Success
        ]);

        $ledgerEntry = LedgerEntry::query()
            ->where(
                'reference',
                $transaction->reference
            )
            ->lockForUpdate()
            ->first();


        if (!$ledgerEntry) {


            LedgerEntry::create([
                'account_id' => $account->id,
                'transaction_id' => $transaction->id,

                'reference' => $transaction->reference,

                'type' => $transaction->type,

                'direction' => $transaction->direction,

                'amount' => $transaction->amount,

                'currency' => $transaction->currency,

                'description' =>
                    'Transaction completed',
            ]);




            $transaction->hold()->update([
                'status' => 'released',
                'released_at' => now(),
            ]);
        }

        $transaction->update([
            'status' => 'successful',
            'completed_at' => now(),
        ]);

        $webhook->update([
            'processed_at' => now(),
            'processing_error' => null,
        ]);

        $transaction->user()->notify(new MoneyMovementAlert(clientName:$transaction->user()->full_name , direction:$transaction->direction , amount:$transaction->amount, mode:$transaction->status->value));
    });

    }
}
