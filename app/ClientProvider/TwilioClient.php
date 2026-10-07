<?php

namespace App\ClientProvider;

use Twilio\Rest\Client;

require __DIR__.'../Sdks/twilio-php-main/src/Twilio/autoload.php';

class TwilioClient
{
    protected string $baseUrl;

    protected array $headers = [];

    protected string $accountSid = '';

    protected string $authToken = '';

    protected object $client;

    public function __construct()
    {
        $this->accountSid = config('twilio.account_sid');
        $this->authToken = config('twilio.auth_token');
        $this->client = new Client($this->accountSid, $this->authToken);
    }

    public function sendPhoneVerificationCode(string $phone_number)
    {
        $verification = $this->client->verify->v2->services(config('twilio.service_sid'))
            ->verifications
            ->create($phone_number, 'sms');

        return $verification->status;
    }
}
