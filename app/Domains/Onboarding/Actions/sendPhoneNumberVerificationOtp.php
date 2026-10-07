<?php

namespace App\Domains\Onboarding\Actions;

use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification;
use App\Models\User;

class sendPhoneNumberVerificationOtp
{
    protected mixed $verifyPhoneNumber;

    public function __construct(PhoneNumberVerification $verifyPhoneNumber)
    {
        $this->verifyPhoneNumber = $verifyPhoneNumber;
    }

    public function execute(mixed $phone_number , User $user , bool $registerPhoneNumber = true )
    {
        return $this->verifyPhoneNumber->sendVerificationCode(phone_number: $phone_number , registerPhoneNumber: $registerPhoneNumber, user: $user);
    }
}
