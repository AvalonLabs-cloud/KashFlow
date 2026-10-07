<?php

namespace App\Domains\Transfer\Eligibility;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use App\AccountStatus;

class AccountStatusEligibility
{

    public function check(?Account $account = null): void
    {
        $account = $account ?? Auth::guard('web')->user()?->account;

        $this->checkOnboardingCompleted($account);
        $this->checkPhoneVerified($account);
        $this->checkAccountIsActive($account);
    }


    protected function checkOnboardingCompleted(Account $account): void
    {
        $onboarding = $account->user->onboarding;

        if ($onboarding === null) {
            throw new RuntimeException(
                'Transaction  eligibility failed: the account has not completed onboarding because no onboarding record exists.'
            );
        }

        if ($onboarding->is_completed === false) {
            throw new RuntimeException(
                'Transaction  eligibility failed: the account has not completed onboarding.'
            );
        }
    }


    protected function checkPhoneVerified(Account $account): void
    {
        $onboarding = $account->user->onboarding;

        if ($onboarding->is_phone_verified === false) {
            throw new RuntimeException(
                'Transaction eligibility failed: the account phone number has not been verified.'
            );
        }
    }


    protected function checkAccountIsActive(Account $account): void
    {
        if ($account->status !== AccountStatus::Active) {
            throw new RuntimeException(
              'Transaction eligibility failed: the account is not active. Current account status: %s.',
                
            );
        }
    }
}
