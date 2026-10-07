<?php

namespace App\Integrations\PhoneVerification\Contracts;

use App\Models\User;

interface PhoneNumberVerification
{
    public function sendVerificationCode( User $user , bool $registerPhoneNumber = true ,string $phone_number='');

    public function verifyVerificationCode(string $verification_code, User $user);
}
