<?php

namespace App\Domains\Onboarding\Actions;

use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification;
use App\Models\User;

class verifyPhoneNumberVerificationOtp
{
    protected mixed $verifyPhoneNumber;

    public function __construct(PhoneNumberVerification $verifyPhoneNumber)
    {
        $this->verifyPhoneNumber = $verifyPhoneNumber;
    }

    public function execute(string $verification_code , User $user)
    {
        return $this->verifyPhoneNumber->verifyVerificationCode(verification_code:  $verification_code , user: $user);
    }
}
