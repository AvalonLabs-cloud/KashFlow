<?php

namespace App\Concerns;
use App\Domains\Transfer\Eligibility\Orchestrator\EligibilityOrchestrator as OrchestratorEligibilityOrchestrator;

trait CanVerifyAccountEligibility
{
    public function checkAccountStatusEligibility(){
        app(OrchestratorEligibilityOrchestrator::class)->checkAccountStatusEligibility();
    }
}
