<?php

namespace App\Concerns;
use App\Models\Account;
use App\Domains\Transfer\Eligibility\Orchestrator\EligibilityOrchestrator\EligibilityOrchestrator;
use Throwable;

trait CanVerifyTransferTransactionEligibility
{
    /**
     * This trait Instanciates the class that is responsible for coordinating the process that ensures a user is eligible to make a transaction inpect the class below to better understand exactly what this method does.
     *
     * @return bool
     */
    public function verifyTransferTransactionEligability(): bool|array{
        try {
            $eligibilityOrchestrator = app(EligibilityOrchestrator::class);

            $eligibilityOrchestrator->check(account: $this , user: $this->user ?? []);

            return true;
        } catch (Throwable $e) {
            return [
                'eligible' => false,
                'reason' => $e->getMessage(),
            ];
        }
       return true;
    }
}
