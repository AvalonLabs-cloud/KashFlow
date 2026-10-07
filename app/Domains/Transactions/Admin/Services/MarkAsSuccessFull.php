<?php
namespace App\Domains\Transactions\Admin\Services;

use App\AdminHeldBalanceStatus;
use App\AdminTransactionStatus;
use Illuminate\Support\Facades\DB;
use App\Models\AdminHeldBalance;
use App\Models\AdminTransaction;

class  MarkAsSuccessFull
{

public function markAsSuccessfull(
    string $transactionReference,
): void {
    DB::transaction(function () use (
        $transactionReference,
    ) {

        $AdminTransaction = AdminTransaction::query()
            ->lockForUpdate()
            ->where('tx_reference', $transactionReference);

            $AdminTransaction->update([
                'status' => AdminTransactionStatus::Success
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
