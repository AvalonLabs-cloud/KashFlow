<?php
namespace App\LocalTesting;
use Illuminate\Support\Str;
use App\Jobs\SimulateBvnUpdateVerificationStatus;
use App\Models\BvnConsent;

class LocalTesting
{
    public function SimulateInitiateBvnVerificationConsent()
    {
       $response = [
            'status' => true,
            'message' => 'BVN verification consent initiated successfully',
            'data' => [
                'reference' => Str::random(),
            ],
        ];
        return $response;
    }
    public function SimulateVerifyBvnConsentJob(BvnConsent $bvnConsent)
    {
       SimulateBvnUpdateVerificationStatus::dispatch($bvnConsent)->delay(now()->addSeconds(40));
    }
}
