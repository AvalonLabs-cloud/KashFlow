<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GenerateBeneficiariesForDemo
{
    public function generateBeneficiaries(
        int $count,
        int $userId = 1
    ): array {
        $records = [];

        for ($i = 0; $i < $count; $i++) {
            $type = fake()->randomElement([
                'transfer',
                'airtime',
                'data',
                'bill',
            ]);

            $record = [
                'user_id' => $userId,
                'type' => $type,
                'recipient_name' => fake()->name(),

                'recipient_account' => null,
                'recipient_bank' => null,
                'recipient_phone' => null,
                'recipient_network' => null,
                'metadata' => null,

                'is_favorite' => fake()->boolean(30),

                'last_used_at' => fake()
                    ->optional(0.7)
                    ->dateTimeBetween('-6 months', 'now'),
            ];

            switch ($type) {
                case 'transfer':
                    $record['recipient_account'] =
                        fake()->numerify('##########');

                    $record['recipient_bank'] = fake()->randomElement([
                        'Access Bank',
                        'GTBank',
                        'First Bank',
                        'UBA',
                        'Zenith Bank',
                        'OPay',
                        'Kuda Bank',
                        'Moniepoint',
                        'PalmPay',
                    ]);

                    break;

                case 'airtime':
                case 'data':
                    $record['recipient_phone'] =
                        fake()->numerify('080########');

                    $record['recipient_network'] = fake()
                        ->randomElement([
                            'MTN',
                            'Airtel',
                            'Glo',
                            '9mobile',
                        ]);

                    break;

                case 'bill':
                    $record['recipient_phone'] =
                        fake()->numerify('080########');

                    $record['metadata'] = json_encode([
                        'provider' => fake()->randomElement([
                            'IKEDC',
                            'EKEDC',
                            'AEDC',
                            'PHED',
                        ]),
                        'customer_number' =>
                            fake()->numerify('############'),
                    ], JSON_THROW_ON_ERROR);

                    break;
            }

            $records[] = $record;
        }

        return $records;
    }
}
