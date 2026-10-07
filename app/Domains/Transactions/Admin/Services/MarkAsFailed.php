<?php

namespace App\Domains\Transactions\Admin\Services;

use App\AdminHeldBalanceStatus;
use App\Models\ProviderOperation;
use App\Models\Transaction;
use App\AdminTransactionStatus;
use App\Models\Hold;
use Illuminate\Support\Facades\DB;
use App\HoldStatus;
use App\Models\AdminHeldBalance;
use App\Models\AdminTransaction;

class MarkAsFailed
{

    public function markAsFailed(
        string $transactionReference,
    ): void {
        DB::transaction(function () use (
            $transactionReference,
        ) {

            $AdminTransaction = AdminTransaction::query()
                ->lockForUpdate()
                ->where('tx_reference', $transactionReference);

            $AdminTransaction->update([
                'status' => AdminTransactionStatus::Failed
            ]);


            $hold = AdminHeldBalance::query()
                ->where('admin_transaction_id',  $AdminTransaction->id)
                ->where('status', AdminHeldBalanceStatus::Active)
                ->lockForUpdate()
                ->first();


            if ($hold) {
                $hold->update([
                    'status' => AdminHeldBalanceStatus::Released,
                    'released_at' => now(),
                ]);
            }
        });
    }
}
