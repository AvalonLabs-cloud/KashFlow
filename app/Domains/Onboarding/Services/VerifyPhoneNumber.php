<?php

namespace App\Domains\Onboarding\Implementations\Twilio;

use App\Domains\Onboarding\Contracts\PhoneVerification as ContractsPhoneVerification;

class VerifyPhoneNumber
{
    protected $phoneVerification;

    public function __construct(ContractsPhoneVerification $phoneVerification)
    {
        $this->phoneVerification = $phoneVerification;
    }

    public function sendCode(mixed $phone_number)
    {
        return $this->phoneVerification->sendVerificationCode($phone_number);
    }

    public function verifyCode(mixed $phone_number, mixed $code)
    {
        return $this->phoneVerification->verifyCode($phone_number, $code);
    }
}
