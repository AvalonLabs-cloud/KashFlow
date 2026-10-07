<?php
namespace App\Domains\Transfer\Eligibility\Orchestrator;
use App\Domains\Transfer\Eligibility\AccountStatusEligibility;
use App\Domains\Transfer\Eligibility\AvailableBalance;
use Illuminate\Support\Facades\Log;

final class EligibilityOrchestrator
{
    public function __construct(
    ) {
    }

    public function checkAccountStatusEligibility(){
        app(AccountStatusEligibility::class)->check();
    }

       public function checkAvailableBalanceEligibility()
    {
        Log::info('hitting available orchestartor');
        $availableBalanceEligibility = app(AvailableBalance::class);
            Log::info('hitting available orchestartor 1.5');
        $availableBalanceEligibility->check();
          Log::info('hitting available orchestartor 2');
    }

}
