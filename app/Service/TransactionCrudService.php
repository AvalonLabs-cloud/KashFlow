<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;

class TransactionCrudService
{
    /**
     * Create a transaction
     */
    public static function create(array $data): int
    {
        return DB::table('transactions')->insertGetId([
            'user_id' => $data['user_id'] ?? null,
            'account_id' => $data['account_id'] ?? null,

            'flw_transaction_id' => $data['flw_transaction_id'] ?? null,
            'reference' => $data['reference'] ?? null,

            'amount' => $data['amount'] ?? '100.00',
            'currency' => $data['currency'] ?? 'NGN',

            'type' => $data['type'] ?? null,
            'status' => $data['status'] ?? 'pending',

            'narration' => $data['narration'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,

            'flutterwave_transaction_fee' => $data['flutterwave_transaction_fee'] ?? null,
            'application_owner_transaction_fee' => $data['application_owner_transaction_fee'] ?? null,

            'webhook_payload' => isset($data['webhook_payload'])
                ? json_encode($data['webhook_payload'])
                : null,

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Find transaction by ID
     */
    public static function find(int $id)
    {
        return DB::table('transactions')->where('id', $id)->first();
    }

    public static function getTransactionReference(int $id)
    {
        return DB::table('transactions')->where('id', $id)->value('reference');
    }

    /**
     * Find by reference (very important for fintech idempotency)
     */
    public static function findByReference(string $reference)
    {
        return DB::table('transactions')
            ->where('reference', $reference)
            ->first();
    }

    /**
     * Update transaction by ID
     */
    public static function update(int $id, array $data): bool
    {
        return DB::table('transactions')
            ->where('id', $id)
            ->update(array_merge($data, [
                'updated_at' => now(),
            ]));
    }

    /**
     * Update by reference (preferred in webhook systems)
     */
    public static function updateByReference(string $reference, array $data): bool
    {
        return DB::table('transactions')
            ->where('reference', $reference)
            ->update(array_merge($data, [
                'updated_at' => now(),
            ]));
    }

    /**
     * Delete transaction
     */
    public static function delete(int $id): bool
    {
        return DB::table('transactions')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Get user transactions (paginated)
     */
    public static function forUser(int $userId, int $limit = 20)
    {
        return DB::table('transactions')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark transaction as successful (common webhook operation)
     */
    public static function markSuccessful(string $reference, array $payload = []): bool
    {
        return DB::table('transactions')
            ->where('reference', $reference)
            ->update([
                'status' => 'successful',
                'webhook_payload' => json_encode($payload),
                'updated_at' => now(),
            ]);
    }

    /**
     * Mark transaction as failed
     */
    public static function markFailed(string $reference, array $payload = []): bool
    {
        return DB::table('transactions')
            ->where('reference', $reference)
            ->update([
                'status' => 'failed',
                'webhook_payload' => json_encode($payload),
                'updated_at' => now(),
            ]);
    }
}
