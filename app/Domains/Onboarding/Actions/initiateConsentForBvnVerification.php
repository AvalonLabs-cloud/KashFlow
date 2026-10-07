<?php

namespace App\Domains\Onboarding\Actions;
use App\Domains\Onboarding\Services\initiateConsentForBvnVerification as initiateConsentForBvnVerificationService;
use App\Models\User;

class initiateConsentForBvnVerification
{

    public function __construct(protected initiateConsentForBvnVerificationService $initiateConsentForBvnVerificationService)
    {

    }
    public function execute( User $user , array $data)
    {
        return $this->initiateConsentForBvnVerificationService->execute(user: $user , data: $data);

    }
}
