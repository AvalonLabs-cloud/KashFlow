<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;



class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    public function definition(): array
    {
        $type = fake()->randomElement([
            'transfer',
            'airtime',
            'data',
        ]);

        return match ($type) {
            'transfer' => [
                'user_id' => 1,
                'type' => $type,
                'recipient_name' => fake()->name(),
                'recipient_account' => fake()->numerify('##########'),
                'recipient_bank' => fake()->randomElement([
                    'Access Bank',
                    'GTBank',
                    'First Bank',
                    'UBA',
                    'Zenith Bank',
                    'OPay',
                    'Kuda Bank',
                    'Moniepoint',
                    'Palmpay',
                ]),
                'recipient_phone' => null,
                'recipient_network' => null,
                'metadata' => null,
                'is_favorite' => fake()->boolean(30),
                'last_used_at' => fake()->optional(0.7)->dateTimeBetween('-6 months', 'now'),
            ],

            'airtime', 'data' => [
                'user_id' => 1,
                'type' => $type,
                'recipient_name' => fake()->name(),
                'recipient_account' => null,
                'recipient_bank' => null,
                'recipient_phone' => fake()->numerify('080########'),
                'recipient_network' => fake()->randomElement([
                    'MTN',
                    'Airtel',
                    'Glo',
                    '9mobile',
                ]),
                'metadata' => null,
                'is_favorite' => fake()->boolean(30),
                'last_used_at' => fake()->optional(0.7)->dateTimeBetween('-6 months', 'now'),
            ],

            'bill' => [
                'user_id' => 1,
                'type' => $type,
                'recipient_name' => fake()->name(),
                'recipient_account' => null,
                'recipient_bank' => null,
                'recipient_phone' => fake()->numerify('080########'),
                'recipient_network' => null,
                'metadata' => [
                    'provider' => fake()->randomElement([
                        'IKEDC',
                        'EKEDC',
                        'AEDC',
                        'PHED',
                    ]),
                    'customer_number' => fake()->numerify('############'),
                ],
                'is_favorite' => fake()->boolean(30),
                'last_used_at' => fake()->optional(0.7)->dateTimeBetween('-6 months', 'now'),
            ],
        };
    }
}
