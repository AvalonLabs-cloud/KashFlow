<?php

namespace App\Http\Middleware\Services\ShapeVerifiers;

use App\Domains\Transfer\Utility\Utility;
use InvalidArgumentException;

class VerifyTransferMoneyDataShape
{
    public function execute(array $data): void
    {
        $requiredKeys = Utility::requiredFieldsForTransferMoneyTypeRequest();

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


        // Validate account number.
        if (
            !isset($data['accountNumber']) ||
            !is_string($data['accountNumber']) ||
            !preg_match('/^\d{10}$/', $data['accountNumber'])
        ) {
            throw new InvalidArgumentException(
                'Account number must be a string containing exactly 11 digits.'
            );
        }

        // Validate bank code.
        if (
            !isset($data['bankCode']) ||
            !is_string($data['bankCode']) ||
            !preg_match('/^\d{3}$/', $data['bankCode'])
        ) {
            throw new InvalidArgumentException(
                'Bank code must be a string containing exactly 3 digits.'
            );
        }


        if (
            !isset($data['recipientname']) ||
            !is_string($data['recipientname']) ||
            trim($data['recipientname']) === ''
        ) {
            throw new InvalidArgumentException(
                'Amount name must be a non-empty string.'
            );
        }

        if (
            !isset($data['amount']) ||
            !is_string($data['amount']) ||
            trim($data['amount']) === ''
        ) {
            throw new InvalidArgumentException(
                'amount must be a non-empty string.'
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
