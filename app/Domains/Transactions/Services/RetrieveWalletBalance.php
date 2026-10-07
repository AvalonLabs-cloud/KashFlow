<?php

namespace App\Domains\Transactions\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RetrieveWalletBalance
{
    public User $user;

    public function __construct()
    {
        if (Auth::check()) {

            $this->user = auth('web')->user();
        }
    }

    public function calculateAvailableBalance(): int|float
    {
        $credits = $this->calculateTransactionCredits();

        $debits = $this->calculateTransactionDebits();

        $heldBalance = $this->calculateHeldBalance();

        return $credits - $debits - $heldBalance;
    }

    public function calculateTransactionDebits(): int|float
    {
        return $this->user
            ->transactions()
            ->where('direction', 'debit')
            ->where('status', 'successful')
            ->sum('amount');
    }
    public function calculateTransactionCredits(): int|float
    {
        return $this->user
            ->transactions()
            ->where('direction', 'credit')
            ->where('status', 'successful')
            ->sum('amount');
    }

    public function calculateHeldBalance(): int|float
    {
        return $this->user
            ->heldBalances()
            ->where('status', 'active')
            ->sum('amount');
    }
}
