<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreditTransaction extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::find(1)->transactions()->create([
            'account_id' => 1,
            'flw_transaction_id' => 'FLW123456789',
            'reference' => 'REF123456789',
            'amount' => 10000.00,
            'currency' => 'USD',
            'type' => 'credit',
            'status' => 'successful',
            'narration' => 'Test credit transaction',
        ]);
    }
}
