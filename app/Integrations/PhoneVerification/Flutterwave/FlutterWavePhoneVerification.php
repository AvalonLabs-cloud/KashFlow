<?php

namespace App\Integrations\PhoneVerification\Flutterwave;

use App\ClientProvider\FlutterwaveClient;
use App\Integrations\PhoneVerification\Adapters\SendOtpResponseAdapter;
use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification;
use App\Models\User;
use App\Transactions\CreateOtpRecord;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\OnboardingPhase;

class FlutterWavePhoneVerification implements PhoneNumberVerification
{
    protected object $client;

    public function __construct(FlutterwaveClient $client)
    {
        $this->client = $client;
    }

    public function sendVerificationCode(User $user, bool $registerPhoneNumber = true, string $phone_number = '')
    {
        if ($registerPhoneNumber) {
            $user->phone()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'phone_number' => $phone_number,
            ]);
        }

        Log::info('user phone', [$user->phone()->first()->phone_number]);

        $userPhoneNumber = $user->phone()->first()->phone_number;
        $phone_number = $userPhoneNumber;
        $response = $this->client->sendVerificationOtp(endpoint: '/otps', includeAuth: true, phone_number: $phone_number);
        Log::info('response data', $response);
        $CreateOtpRecord = new CreateOtpRecord;
        $result = $CreateOtpRecord->execute($response, $user);
        if ($result['status']) {
            return SendOtpResponseAdapter::parse($response);
        } else {
            return [
                'status' => false,
            ];
        }
    }

    public function verifyVerificationCode(string $verification_code, User $user)
    {
        $latest_otp = $user->otps()->latest()->first();
        Log::info('user for latest otp', [$user->otps]);
        Log::info('latest otp', [$latest_otp]);
        if ($verification_code !== $latest_otp->otp) {
            return [
                'status' => false,
                'message' => 'otp does not match',
            ];
        };

        if (!$latest_otp->expiration->isFuture()) {
            return [
                'status' => false,
                'message' => 'otp is expired',
            ];
        };

        $otp_validation_result =  $this->client->verifyVerificationCode(includeAuth: true, endpoint: "/otps/$latest_otp->reference/validate", payload: ['otp' => $verification_code]);
        if ($otp_validation_result['status']) {
            $user->onboarding->update(['is_phone_verified' => true]);
            $user->onboarding->update(['phase' => OnboardingPhase::PHASE_FOUR]);
        }
    }
}
