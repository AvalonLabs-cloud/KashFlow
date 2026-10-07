<?php

namespace App\Integrations\PhoneVerification\Twilio;

use App\ClientProvider\TwilioClient;
use App\Integrations\PhoneVerification\Adapters\TwilioPhoneverificationAdapter;
use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification;

class TwilioPhoneVerification implements PhoneNumberVerification
{
    protected TwilioClient $client;

    protected function __construct(TwilioClient $client) {}

    public function sendVerificationCode(string $phone_number)
    {
        $response = $this->client->sendPhoneVerificationCode($phone_number);

        return TwilioPhoneverificationAdapter::TwilioPhoneverificationAdapter($response);
    }

    public function verifyCode(string $phone_number, string $code)
    {
        // Implement Twilio API call to verify the code
    }
}
