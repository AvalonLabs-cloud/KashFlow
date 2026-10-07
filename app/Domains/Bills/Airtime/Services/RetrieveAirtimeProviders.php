<?php

namespace App\Domains\Bills\Airtime\Services;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Str;

class RetrieveAirtimeProviders
{
    public function __construct(
        public FlutterwaveClient $flutterwaveClient
    ) {}

    public function excecute()
    {
        $airtimeProviderList = $this->getAirtimeProvidersList();
        if ($airtimeProviderList['status'] === 'success') {
            $airtime_providers_list_collection = collect($airtimeProviderList['data']);
            $code_provider_list = $airtime_providers_list_collection->mapWithKeys(function ($item) {
                $item_name = Str::remove('NIGERIA', $item['name']);

                return [$item['biller_code'] => $item_name];
            });
        }

        return $code_provider_list ?? [];
    }

    private function getAirtimeProvidersList()
    {
        return $this->flutterwaveClient->getBillProvidersList(billType: config('bill.supported_airtime_code.airtime'));
    }
}
