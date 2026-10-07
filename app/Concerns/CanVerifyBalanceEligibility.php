<?php

namespace App\Concerns;
use App\Domains\Transfer\Eligibility\Orchestrator\EligibilityOrchestrator as OrchestratorEligibilityOrchestrator;
use Illuminate\Support\Facades\Log;

trait CanVerifyBalanceEligibility
{

    public function checkAvailableBalanceEligibility(){
        Log::info('hitting concern');
        app(OrchestratorEligibilityOrchestrator::class)->checkAvailableBalanceEligibility();
    }
}