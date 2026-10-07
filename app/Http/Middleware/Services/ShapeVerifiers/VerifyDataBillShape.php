<?php

namespace App\Http\Middleware\Services\ShapeVerifiers;

use InvalidArgumentException;

class VerifyDataBillShape
{
    public function execute(array $data): void
    {
        $requiredKeys = [
            'phoneNumber',
            'itemCode',
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
        //     !isset($data['phoneNumber']) ||
        //     !is_string($data['phoneNumber']) ||
        //     !preg_match('/^\d{11}$/', $data['phoneNumber'])
        // ) {
        //     throw new InvalidArgumentException(
        //         'Phone number must contain exactly 11 digits.'
        //     );
        // }

        // Validate item code.
        if (
            !isset($data['itemCode']) ||
            !is_string($data['itemCode']) ||
            trim($data['itemCode']) === ''
        ) {
            throw new InvalidArgumentException(
                'Item code must be a valid non-empty string.'
            );
        }

        // Validate transaction type.
        if (
            !isset($data['transactionType']) ||
            !is_string($data['transactionType']) ||
            trim($data['transactionType']) === ''
        ) {
            throw new InvalidArgumentException(
                'Transaction type must be a valid non-empty string.'
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
