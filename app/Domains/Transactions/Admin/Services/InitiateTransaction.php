<?php

namespace App\Domains\Transactions\Admin\Services;

use App\AdminHeldBalanceStatus;
use App\AdminTransactionStatus;
use App\Models\AdminTransaction;
use App\Models\AdminHeldBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;

class InitiateTransaction
{
    public function execute()
    {
        return DB::transaction(function () {
            User::where('is_admin', true)->lockForUpdate();

            $availableBalance = $this->getAvailableBalance();

            if ($availableBalance <= 0) {
                return redirect()->route('filament.pages.admin-cash-out')
                    ->with('error', 'funds insufficient');
            }

            $transactionReference = $this->generateTransactionReference();

            $adminTransaction =  AdminTransaction::create([
                'admin_id' => User::where('is_admin', true)->first()->id,
                'tx_reference' => $this->generateTransactionReference(),
                'direction' => 'debit',
                'status' => AdminTransactionStatus::Pending,
                'amount' => $availableBalance,
            ]);

            $adminHeldBalance = AdminHeldBalance::create([
                'admin_transction_id' => $adminTransaction->id,
                'status' => AdminHeldBalanceStatus::Active,
                'amount' => $adminTransaction->amount,
            ]);

            return [
                'transaction' => $adminTransaction,
                'hold' =>   $adminHeldBalance,
            ];
        });
    }

    public function getAdminUser(): ?User
    {
        return User::where('is_admin', true)->first();
    }

    private function getAvailableBalance()
    {
        $admin = $this->getAdminUser();

        if (!$admin) {
            return 0;
        }

        $totalCredits = (int) AdminTransaction::where('admin_id', $admin->id)
            ->where('status', AdminTransactionStatus::Success)
            ->where('direction', 'credit')
            ->sum('amount');

        $totalDebits = (int) AdminTransaction::where('admin_id', $admin->id)
            ->where('status', AdminTransactionStatus::Success)
            ->where('direction', 'debit')
            ->sum('amount');

        $totalHeld = (int) AdminHeldBalance::where('status', 'active')
            ->sum('amount');

        return $totalCredits - $totalDebits - $totalHeld;
    }


    private function generateTransactionReference(): string
    {
        return 'TXN-' . Str::upper(Str::random(24));
    }
}
