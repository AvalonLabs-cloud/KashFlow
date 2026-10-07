<?php

namespace App\Http\Middleware\Services\ShapeVerifiers;

use InvalidArgumentException;

class VerifyAirtimeShape
{
    public function execute(array $data): void
    {
        $requiredKeys = [
            'amount',
            'phoneNumber',
            'selectedProvider',
            'transactionType',
        ];

        $dataKeys = array_keys($data);

        sort($dataKeys);

        $sortedRequiredKeys = $requiredKeys;
        sort($sortedRequiredKeys);

        $requiredKeysWithPin = [
            ...$requiredKeys,
            'transaction_pin',
            'transaction_timestamp',
            'transaction_signature',
        ];

        sort($requiredKeysWithPin);

        if (
            $dataKeys !== $sortedRequiredKeys &&
            $dataKeys !== $requiredKeysWithPin
        ) {
            throw new InvalidArgumentException(
                'Invalid transfer request data shape.'
            );
        }

        if (count($dataKeys) !== count(array_unique($dataKeys))) {
            throw new InvalidArgumentException(
                'Duplicate keys are not allowed.'
            );
        }


        // if (
        //     !isset($data['amount']) ||
        //     !is_string($data['amount']) ||
        //     !preg_match('/^\d+$/', $data['amount'])
        // ) {
        //     throw new InvalidArgumentException(
        //         'Amount must contain only numeric characters.'
        //     );
        // }

        if (
            !isset($data['phoneNumber']) ||
            !is_string($data['phoneNumber']) ||
            !preg_match('/^\d{11}$/', $data['phoneNumber'])
        ) {
            throw new InvalidArgumentException(
                'Phone number must contain exactly 11 digits.'
            );
        }

        if (
            !isset($data['selectedProvider']) ||
            !is_string($data['selectedProvider']) ||
            trim($data['selectedProvider']) === ''
        ) {
            throw new InvalidArgumentException(
                'Selected provider must be a valid string.'
            );
        }

        if (isset($data['transaction_pin'])) {
            if (
                !is_string($data['transaction_pin']) ||
                !preg_match('/^\d{4}$/', $data['transaction_pin'])
            ) {
                throw new InvalidArgumentException(
                    'Bank code must be a string containing exactly 6 digits.'
                );
            }
        }
    }
}
