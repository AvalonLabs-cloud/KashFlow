<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = fake()->randomElement([
            'airtime',
            'data',
            'transfer',
        ]);

        $direction = fake()->randomElement([
            'credit',
            // 'debit',
        ]);

        $status = fake()->randomElement([
            'pending',
            'successful',
            'failed',
        ]);

        $initiatedAt = fake()->dateTimeBetween('-30 days', 'now');

        // Only successful transactions get a completed_at timestamp
        $completedAt = $status === 'successful'
            ? fake()->dateTimeBetween($initiatedAt, 'now')
            : null;

        // Only failed transactions get a failed_at timestamp
        $failedAt = $status === 'failed'
            ? fake()->dateTimeBetween($initiatedAt, 'now')
            : null;

        return [
            'user_id' => 1,
            'account_id' => 1,

            'transaction_reference' => 'TXN-' . strtoupper(fake()->unique()->bothify('??##########')),

            'type' => $type,

            'direction' => $direction,

            // Amount is stored in kobo
            'amount' => fake()->numberBetween(
                500 * 100,
                500000 * 100
            ),

            'status' => $status,

            'base_currency' => 'naira',
            'ledger_currency' => 'kobo',

            'recipient' => match ($type) {
                'airtime' => fake()->numerify('080########'),
                'data' => fake()->numerify('080########'),
                'transfer' => fake()->name(),
            },

            'metadata' => [
                'provider' => fake()->randomElement([
                    'flutterwave',
                    'internal',
                ]),
                'channel' => fake()->randomElement([
                    'mobile',
                    'web',
                    'api',
                ]),
            ],

            'description' => match ($type) {
                'airtime' => 'Airtime purchase',
                'data' => 'Data bundle purchase',
                'transfer' => $direction === 'debit'
                    ? 'Money transfer'
                    : 'Money received',
                'cable' => 'Cable TV subscription',
                'electricity' => 'Electricity bill payment',
            },

            'failure_reason' => $status === 'failed'
                ? fake()->randomElement([
                    'Insufficient funds',
                    'Provider service unavailable',
                    'Transaction could not be completed',
                    'Transaction timed out',
                ])
                : null,

            'initiated_at' => $initiatedAt,
            'completed_at' => $completedAt,
            'failed_at' => $failedAt,
        ];
    }
}

