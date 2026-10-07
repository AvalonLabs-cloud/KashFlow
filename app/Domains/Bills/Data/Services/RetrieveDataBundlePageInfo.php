<?php

namespace App\Domains\Bills\Data\Services;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Support\Str;

class RetrieveDataBundlePageInfo
{
    public function __construct(
        protected FlutterwaveClient $flutterwaveClient,
    ) {}

    public function execute(string $dataProvider = 'MTN')
    {
        $data_service_providers = $this->flutterwaveClient->getBillProvidersList(billType: config('bill.data_bundle_category'));
        $data_service_providers_list = collect($data_service_providers['data'])->pluck('short_name')->toArray();
        $data_providers_list = collect($data_service_providers_list)->map(function ($provider) {
            return Str::remove('DATA BUNDLE', $provider);
        })->filter();
        $data_service_provider_biller_code = collect($data_service_providers['data'])->where('short_name', $dataProvider . ' DATA BUNDLE')->pluck('biller_code')->first();
        $data_service_provider_items = $this->flutterwaveClient->getItemCodesForBill(billerCode: $data_service_provider_biller_code);
        $data_provider_categories = collect($data_service_provider_items['data'])->pluck('validity_period')->unique()->toArray();
        $data_map = collect($data_service_provider_items['data'])->mapWithKeys(function ($item) {
            return [$item['item_code'] => [
                'name' => $item['name'],
                'amount' => $item['amount'],
                'validity_period' => $item['validity_period'],
            ]];
        });

        return [
            'data_providers_list' => $data_providers_list,
            'data_provider_categories' => $data_provider_categories,
            'data_map' => $data_map,
        ];
    }
}
