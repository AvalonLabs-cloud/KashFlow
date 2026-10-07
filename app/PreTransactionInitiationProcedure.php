<?php

namespace App;

use App\Models\HeldBalance;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PreTransactionInitiationProcedure
{
    public static function isBalanceSufficient()
    {
        $credit_summation = Transaction::where('user_id', Auth::user()->id)->where('status', TransactionStatus::SUCCESSFUL)->where('type', 'credit')->sum('amount');
        $debit_summation = Transaction::where('user_id', Auth::user()->id)->where('status', TransactionStatus::SUCCESSFUL)->where('type', 'debit')->sum('amount');
        $current_balance = $credit_summation - $debit_summation;

        return $current_balance;

    }

    public static function isAvailableBalanceSufficient()
    {
        $current_balance = self::isBalanceSufficient();
        $held_balance = HeldBalance::where('user_id', Auth::user()->id)->where('status', 'held')->sum('amount');
        $available_balance = $current_balance - $held_balance;

        return $available_balance;

    }

    public static function createRecordsForAirtime(?int $amount, ?string $transaction_type)
    {
        $result = DB::transaction(function () use ($amount, $transaction_type) {
            // Create held_balance  record
            HeldBalance::create([
                'user_id' => Auth::user()->id,
                'amount' => $amount,
                'status' => 'held',
                'reference' => Str::uuid(),
            ]);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => Auth::user()->id,
                'amount' => $amount,
                'type' => 'debit',
                'status' => TransactionStatus::PENDING,
                'reference' => Str::uuid(),
                'extra_data' => [
                    'transaction_type' => $transaction_type,
                    'airtime_amount' => '300',
                    'network' => 'MTN',
                    'phone_number' => '08012345678',
                    'total_including_fees' => '350',
                ],
            ]);

            $ledgerEntry = $transaction->ledgers()->create([
                'user_id' => Auth::user()->id,
                'amount' => $amount,
                'type' => 'debit',
                // 'request_identifier' => Str::uuid(),
                'transaction_context' => $transaction_type,
            ]);

            return ['transaction_reference' => $transaction->reference->toString(), 'ledger_request_identifier' => $ledgerEntry->request_identifier];

        }, 5); // Optional: Second argument is number of retry attempts for deadlocks

        return $result;
    }
}
