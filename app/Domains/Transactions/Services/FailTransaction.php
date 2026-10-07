<?php
namespace App\Domains\Transactions\Services;
use App\Models\ProviderOperation;
use App\Models\Transaction;
use App\AdminTransactionStatus;
use App\Models\Hold;
use Illuminate\Support\Facades\DB;
use App\HoldStatus;
class FailTransaction
{

public function markAsFailed(
    string $providerOperationReference,
    string $failureReason,
    string $transactionReference,
    ?array $responsePayload = null,
): void {
    DB::transaction(function () use (
        $providerOperationReference,
        $failureReason,
        $transactionReference,
        $responsePayload,
    ) {

        $providerOperation = ProviderOperation::query()
            ->lockForUpdate()
            ->where('client_reference', $providerOperationReference);


        if ($providerOperation->status !== 'pending') {
            return;
        }


        $transaction = Transaction::query()
            ->lockForUpdate()
            ->where('transaction_reference', $transactionReference);

            $transaction->adminTransaction->update([
                'status' => AdminTransactionStatus::Failed
            ]);


        $hold = Hold::query()
            ->where('transaction_id', $transaction->id)
            ->where('status', HoldStatus::Active)
            ->lockForUpdate()
            ->first();


        $providerOperation->update([
            'status' => 'failed',

            'failure_reason' => $failureReason,

            'response_payload' => $responsePayload,

            'completed_at' => now(),
        ]);

        /*
         * Mark the internal transaction as failed.
         */
        $transaction->update([
            'status' => 'failed',
        ]);


        if ($hold) {
            $hold->update([
                'status' => 'released',
                'released_at' => now(),
            ]);
        }
    });
}
}
