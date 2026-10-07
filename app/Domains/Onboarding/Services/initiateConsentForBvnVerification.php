<?php
namespace App\Domains\Onboarding\Services;
use App\ClientProvider\FlutterwaveClient;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\LocalTesting\LocalTesting;

class initiateConsentForBvnVerification
{

    public function __construct(protected FlutterwaveClient $flutterwaveClient , protected  LocalTesting $localTesting)
    {
    }

    public function execute(User $user , array $data , )
    {
      $createdBvnConsent =  $user->bvnConsent()->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'bvn' => $data['bvn'],
            'extra' => $data['extra'] ?? [],
        ]);

        ['first_name' => $firstname , 'last_name' => $lastname , 'bvn' => $bvn] = $createdBvnConsent->toArray();

        $payload = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'bvn' => $bvn,
        ];

        if (App::environment('local')) {
            $initiateConsentResponse = $this->localTesting->SimulateInitiateBvnVerificationConsent();
            Log::info('Initiate BVN Verification Consent Response: ', ['response' => $initiateConsentResponse]);
            if (isset($initiateConsentResponse['status']) &&  $initiateConsentResponse['status'] === true) {
                $createdBvnConsent->update([
                    'consent_reference' => $initiateConsentResponse['data']['reference'],
                ]);
                $this->localTesting->SimulateVerifyBvnConsentJob(bvnConsent: $createdBvnConsent);
                return [
                    'status' => true,
                    'message' => 'BVN verification consent initiated successfully',
                    'data' => $initiateConsentResponse['data'],
                ];
            } else {
                Log::info('Initiate BVN Verification Consent Failure Response: ', ['response' => $initiateConsentResponse]);
                return [
                    'status' => false,
                    'message' => $initiateConsentResponse['message'] ?? 'Failed to initiate BVN verification consent',
                ];
            }
        }
        else {
         $initiateConsentResponse = $this->flutterwaveClient->initiateBvnVerificationConsent(endpoint: '/bvn/verifications', payload: $payload , includeAuth: true);
        Log::info('Initiate BVN Verification Consent Response: ', ['response' => $initiateConsentResponse]);
        if (isset($initiateConsentResponse['status']) &&  $initiateConsentResponse['status'] === 'success') {
            $createdBvnConsent->update([
                'consent_reference' => $initiateConsentResponse['data']['reference'],
            ]);
            return [
                'status' => true,
                'message' => 'BVN verification consent initiated successfully',
                'data' => $initiateConsentResponse['data'],
                'redirect_url' => $initiateConsentResponse['data']['url'],
            ];
        } else {
            Log::info('Initiate BVN Verification Consent Failure Response: ', ['response' => $initiateConsentResponse]);
            return [
                'status' => false,
                'message' => $initiateConsentResponse['message'] ?? 'Failed to initiate BVN verification consent',
            ];
        }
        }




    }
}
