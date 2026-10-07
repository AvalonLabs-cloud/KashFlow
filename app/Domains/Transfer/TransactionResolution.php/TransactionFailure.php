<?php

namespace App\Domains\Transfer\TransactionResolution;

use App\AdminTransactionStatus;
use Illuminate\Support\Facades\DB;
use App\Models\WebHookEvent;
use App\Models\Transaction;
use App\Models\ProviderOperation;
use App\Notifications\MoneyMovementAlert;

class TransactionFailure
{
    public function execute(mixed $webhook, mixed  $providerTransfer)
    {
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


            if ($transaction->status === 'failed') {



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



            $provider = ProviderOperation::query()
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

            if ($provider->status === 'failed') {

                $webhook->update([
                    'processed_at' => now(),
                    'processing_error' => null,
                ]);

                return;
            }

            $transaction->adminTransaction->update([
                'status' => AdminTransactionStatus::Success
            ]);



            $transaction->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);


            $webhook->update([
                'processed_at' => now(),
                'processing_error' => null,
            ]);

            $transaction->user()->notify(new MoneyMovementAlert(clientName:$transaction->user()->full_name , direction:$transaction->direction , amount:$transaction->amount , mode:$transaction->status));
        });
    }
}
