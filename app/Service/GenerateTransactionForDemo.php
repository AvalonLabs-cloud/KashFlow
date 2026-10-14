<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GenerateTransactionForDemo
{
    public function generateTransactions(
        int $count,
        int $userId = 1,
        int $accountId = 1
    ): array {
        $records = [];

        for ($i = 0; $i < $count; $i++) {
            $type = fake()->randomElement([
                'airtime',
                'data',
                'transfer',
            ]);

            // Approximately 85% successful, 10% pending, 5% failed.
            $status = fake()->randomElement([
                ...array_fill(0, 85, 'successful'),
                ...array_fill(0, 10, 'pending'),
                ...array_fill(0, 5, 'failed'),
            ]);

            $initiatedAt = fake()->dateTimeBetween('-30 days', 'now');

            $completedAt = $status === 'successful'
                ? fake()->dateTimeBetween($initiatedAt, 'now')
                : null;

            $failedAt = $status === 'failed'
                ? fake()->dateTimeBetween($initiatedAt, 'now')
                : null;

            $recipient = match ($type) {
                'airtime', 'data' => fake()->numerify('080########'),
                'transfer' => fake()->name(),
            };

            $records[] = [
                'user_id' => $userId,
                'account_id' => $accountId,

                'transaction_reference' => 'TXN-' .
                    strtoupper(Str::random(12)),

                'type' => $type,
                'direction' => 'credit',

                // Amount stored in kobo.
                'amount' => fake()->numberBetween(
                    500 * 100,
                    500000 * 100
                ),

                'status' => $status,

                'base_currency' => 'naira',
                'ledger_currency' => 'kobo',

                'recipient' => $recipient,

                'metadata' => json_encode([
                    'provider' => fake()->randomElement([
                        'flutterwave',
                        'internal',
                    ]),
                    'channel' => fake()->randomElement([
                        'mobile',
                        'web',
                        'api',
                    ]),
                ], JSON_THROW_ON_ERROR),

                'description' => match ($type) {
                    'airtime' => 'Airtime purchase',
                    'data' => 'Data bundle purchase',
                    'transfer' => 'Money received',
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

        return $records;
    }
}
