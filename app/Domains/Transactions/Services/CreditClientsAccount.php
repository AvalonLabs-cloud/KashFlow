<?php
namespace App\Domains\Transactions\Services;
use App\TransactionStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditClientsAccount
{
    public function execute(string $tx_ref, string $amount, array $metadata)
    {
        return DB::transactions(function () use ($tx_ref,  $amount, $metadata) {
            User::where('is_admin', true)->lockForUpdate();
            $user = User::where('tx_ref', $tx_ref)->first();
            $user->transactions()->create([
                'account_id' => $user->account->id,
                'transaction_reference' => $tx_ref,

                'type' => 'transfer',

                'direction' => 'credit',

                'amount' => $amount,

                'status' => TransactionStatus::SUCCESSFUL,

                'base_currency' => 'NGN',
                'ledger_currency' => 'kobo',

                'metadata' => $metadata,
                'initiated_at' => now(),
            ]);
        });
    }
}
