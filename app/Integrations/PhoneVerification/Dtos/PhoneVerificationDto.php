<?php

namespace App\Integrations\PhoneVerification\Dtos;

class PhoneVerificationDto
{
    public string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }
}
