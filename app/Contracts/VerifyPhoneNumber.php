<?php

namespace App\Contracts;

interface VerifyPhoneNumber
{
    public function retrieveClient(mixed $credentials);

    public function sendVerificationCode(mixed $phone_number);

    public function verifyCode(mixed $phone_number, mixed $code);
}
