<?php
namespace App\Domains\Onboarding\Services;
use App\ClientProvider\FlutterwaveClient;
use App\Domains\Onboarding\Services\CreateAccountRecord;
use App\AccountStatus;
use Illuminate\Support\Facades\Log;

class CompleteOnboarding {
   public function __construct(public FlutterwaveClient $flutterwaveClient , public CreateAccountRecord $createAccountRecord)
   {
      
   }
   public function execute(mixed $data){
    $result =  $this->flutterwaveClient->createPermanentVirtualAccount($data);
    Log::info('account creation result' , $result);
    if ($result['status'] === 'success') {
     $accountCreated = $this->createAccountRecord->execute([
            'user_id' => auth('web')->user()->id,
            'order_ref' => $result['data']['order_ref'],
            'flw_ref' => $result['data']['flw_ref'],
            'account_number' => $result['data']['account_number'],
            'bank_name' => $result['data']['bank_name'],
            'account_name' => $result['data']['note'],
            'status' => AccountStatus::Active,
        ]);

        if ($accountCreated) {
            $accountCreated->user->onboarding()->update([
             'is_completed' => true,
            ]);
            return true;
        }

        return false;
    }
    return false;
   }
}