<?php

namespace App\Domains\Transfer\Eligibility;

use App\Models\User;
use App\Domains\Transfer\Exceptions\InsufficientAvailableBalanceException;
use InvalidArgumentException;
use Illuminate\Http\Request;
use App\Domains\Bills\Data\Services\RetrievePriceOfDataBundle;
use App\TransactionStatus;
use App\HoldStatus;
use Illuminate\Support\Facades\Log;

class AvailableBalance
{
    private User $user;
    private  RetrievePriceOfDataBundle $retrievePriceOfDataBundle;
    protected Request $transactionRequest;

    public function __construct()
    {
        $this->transactionRequest = request();
        $this->user = auth('web')->user();
        $this->retrievePriceOfDataBundle = app(RetrievePriceOfDataBundle::class);
    }

    public function check(): void
    {
        Log::info('request coming to backend' , $this->transactionRequest->toArray());
        $availableBalance = $this->calculateAvailableBalance();
        $this->ensureTransactionCanBeProcessed(
            transactionType: $this->transactionRequest->input('transactionType'),
            availableBalance: $availableBalance,
        );
    }


    public function calculateAvailableBalance()
    {

        $credits = $this->calculateTransactionCredits();

        $debits = $this->calculateTransactionDebits();

        $heldBalance = $this->calculateHeldBalance();

        return $credits - $debits - $heldBalance;
    }

    public function calculateTransactionCredits(): int|float
    {
        return $this->user
            ->transactions()
            ->where('direction', 'credit')
            ->where('status', TransactionStatus::SUCCESSFUL)
            ->sum('amount');
    }

    public function calculateTransactionDebits(): int|float
    {
        return $this->user
            ->transactions()
            ->where('direction', 'debit')
            ->where('status', TransactionStatus::SUCCESSFUL)
            ->sum('amount');
    }


    public function calculateHeldBalance(): int|float
    {
        return $this->user
            ->heldBalances()
            ->where('status', HoldStatus::Active)
            ->sum('amount');
    }


    private function ensureTransactionCanBeProcessed(
        string $transactionType,
      mixed $availableBalance,
    ){
        $transactionAmount = $this->transactionAmount(transactionType: $transactionType);

        if ($transactionAmount > $availableBalance) {
            throw new InsufficientAvailableBalanceException(
                accountId: $this->user->getKey(),
                requestedAmount: $transactionAmount,
                availableBalance: $availableBalance,
            );
        }
    }

    public function transactionAmount(mixed $transactionType)
    {
        if ($transactionType === 'data') {
            return $this->determineDataBillAmount();
        }
        return $this->transactionRequest->input('amount');
    }

    private function determineDataBillAmount()
    {
        return  $this->retrievePriceOfDataBundle->execute(itemCode: $this->transactionRequest->input('itemCode'));
    }
}
