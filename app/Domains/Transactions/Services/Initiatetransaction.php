<?php

namespace App\Domains\Transactions\Services;

use App\AdminTransactionStatus;
use App\Models\Account;
use App\Models\Hold;
use App\Models\ProviderOperation;
use App\Models\Transaction;
use App\Models\AdminTransaction;
use App\Models\TransactionFeeSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use App\HoldStatus;
use App\Models\User;
use App\TransactionStatus;

class InitiateTransaction
{
    public function __construct() {}

    public function execute(
        Account $account,
        string $type,
        int $amount,
        array $metadata = [],
    ): array {
        $this->validateTransactionType($type);

        return DB::transaction(function () use (
            $account,
            $type,
            $amount,
            $metadata
        ) {

            $account = Account::query()
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();


            $availableBalance = $account->walletBalance();

            if ($availableBalance < $amount) {
                throw new RuntimeException(
                    'Insufficient available balance.'
                );
            }

            $transactionReference = $this->generateTransactionReference();

            /*
             * Create the internal transaction.
             */
            $transaction = Transaction::create([
                'user_id' => $account->user_id,
                'account_id' => $account->id,

                'transaction_reference' => $transactionReference,

                'type' => $type,
                'direction' => 'debit',

                'amount' => $amount + TransactionFeeSetting::calculateFee($amount),

                // 'currency' => strtoupper($currency),

                'status' => TransactionStatus::PENDING,

                'base_currency' => 'NGN',
                'ledger_currency' => 'kobo',


                /*
                 * transfer:
                 * [
                 *     'bank_code' => '058',
                 *     'account_number' => '0123456789',
                 *     'account_name' => 'John Doe',
                 * ]
                 *
                 * airtime:
                 * [
                 *     'network' => 'MTN',
                 *     'phone' => '08012345678',
                 * ]
                 *
                 * data:
                 * [
                 *     'network' => 'MTN',
                 *     'phone' => '08012345678',
                 *     'bundle_code' => '...',
                 * ]

                 */
                'metadata' => $metadata,
                'initiated_at' => now(),
            ]);

            $hold = Hold::create([
                'user_id' => $account->user_id,
                'account_id' => $account->id,

                'transaction_id' => $transaction->id,

                'amount' => $amount + TransactionFeeSetting::calculateFee($transaction->amount),

                'base_currency' => 'NGN',
                'ledger_currency' => 'kobo',

                'status' => HoldStatus::Active,

                'held_at' => now(),
            ]);

            AdminTransaction::create([
                'admin_id' => User::where('is_admin' , true)->first()->id,
                'transaction_id' => $transaction->id,
                'direction' => 'credit',
                'status' => AdminTransactionStatus::Pending,
                'amount' => TransactionFeeSetting::calculateFee($transaction->amount),
            ]);


            $clientReference = $this->generateClientReference();


            $providerOperation = ProviderOperation::create([
                'transaction_id' => $transaction->id,

                'provider' => 'flutterwave',

                'operation' => $this->getProviderOperation($type),

                'client_reference' => $clientReference,

                'provider_reference' => null,

                'status' => 'pending',

                'attempt_number' => 1,

                'response_code' => null,

                'failure_reason' => null,

                'request_payload' => null,

                'response_payload' => null,

                'requested_at' => now(),

                'last_attempt_at' => null,

                'completed_at' => null,
            ]);


            $providerRequest = [
                'provider' => 'flutterwave',

                'operation' => $providerOperation->operation,

                'client_reference' =>
                $providerOperation->client_reference,

                'transaction_reference' =>
                $transaction->transaction_reference,

                'amount' => $transaction->amount,

                'recipient' => $transaction->recipient,

                'metadata' => $transaction->metadata,
            ];

            return [
                'transaction' => $transaction,

                'hold' => $hold,

                'provider_operation' => $providerOperation,

                'provider_request' => $providerRequest,
            ];
        });
    }


    private function getProviderOperation(string $type): string
    {
        return match ($type) {

            'transfer' =>
            'bank_transfer',

            'airtime' =>
            'airtime_purchase',

            'data' =>
            'data_purchase',

            default =>
            throw new InvalidArgumentException(
                "Unsupported transaction type: {$type}"
            ),
        };
    }


    private function validateTransactionType(string $type): void
    {
        if (! in_array($type, [
            'transfer',
            'airtime',
            'data',
        ], true)) {
            throw new InvalidArgumentException(
                "Unsupported transaction type: {$type}"
            );
        }
    }


    private function generateTransactionReference(): string
    {
        return 'TXN-' . Str::upper(Str::random(24));
    }


    private function generateClientReference(): string
    {
        return 'PO-' . Str::upper(Str::random(24));
    }


    private function buildDescription(
        string $type,
        string $recipient
    ): string {
        return match ($type) {

            'transfer' =>
            "Transfer to {$recipient}",

            'airtime' =>
            "Airtime purchase for {$recipient}",

            'data' =>
            "Data purchase for {$recipient}",
        };
    }
}
