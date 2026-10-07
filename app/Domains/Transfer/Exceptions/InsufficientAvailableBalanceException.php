<?php

namespace App\Domains\Transfer\Exceptions;

use RuntimeException;

/**
 * Thrown when an account does not have sufficient available
 * balance to process a proposed transaction.
 */
final class InsufficientAvailableBalanceException extends RuntimeException
{
    /**
     * Create a new insufficient-balance exception.
     *
     * @param int|string $accountId
     * @param int|float $requestedAmount
     * @param int|float $availableBalance
     */
    public function __construct(
        private readonly int|string $accountId,
        private readonly int|float $requestedAmount,
        private readonly int|float $availableBalance,
    ) {
        parent::__construct(
            sprintf(
                'Insufficient available balance for account %s. Requested: %s. Available: %s.',
                $accountId,
                $requestedAmount,
                $availableBalance,
            )
        );
    }

    /**
     * Get the account identifier.
     *
     * @return int|string
     */
    public function accountId(): int|string
    {
        return $this->accountId;
    }

    /**
     * Get the requested transaction amount.
     *
     * @return int|float
     */
    public function requestedAmount(): int|float
    {
        return $this->requestedAmount;
    }

    /**
     * Get the available balance at the time of evaluation.
     *
     * @return int|float
     */
    public function availableBalance(): int|float
    {
        return $this->availableBalance;
    }
}
