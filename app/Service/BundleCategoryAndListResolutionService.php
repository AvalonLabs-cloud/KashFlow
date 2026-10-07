<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BundleCategoryAndListResolutionService
{
    protected mixed $data;

    protected array $validity_category_list = ['Daily' => 1, 'Weekly' => 7, 'Monthly' => 30, 'Quarterly' => 90, 'Semiannual' => 180, 'Annual' => 365];

    protected array $categoryListToReturn = [];

    protected mixed $bundleMapToReturn = [];

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    public function getCategory(mixed $validity_period)
    {
        return match ((int) $validity_period) {
            $this->validity_category_list['Daily'] => 'Daily',
            $this->validity_category_list['Weekly'] => 'Weekly',
            $this->validity_category_list['Monthly'] => 'Monthly',
            $this->validity_category_list['Quarterly'] => 'Quarterly',
            $this->validity_category_list['Semiannual'] => 'Semiannual',
            $this->validity_category_list['Annual'] => 'Annual',
            default => null,
        };
    }

    public function packageData()
    {
        $collection = collect($this->data);
        $validity_period_array = $collection->pluck('validity_period')->unique()->values();
        Log::info('collect data providers list', ['data' => $validity_period_array]);
        $validity_period_array->map(function ($item) {
            if ((int) $item === $this->validity_category_list['Daily']) {
                $this->categoryListToReturn[] = 'Daily';
            }
            if ((int) $item === $this->validity_category_list['Weekly']) {
                $this->categoryListToReturn[] = 'Weekly';
            }
            if ((int) $item === $this->validity_category_list['Monthly']) {
                $this->categoryListToReturn[] = 'Monthly';
            }
            if ((int) $item === $this->validity_category_list['Quarterly']) {
                $this->categoryListToReturn[] = 'Quarterly';
            }
            if ((int) $item === $this->validity_category_list['Semiannual']) {
                $this->categoryListToReturn[] = 'Semiannual';
            }
            if ((int) $item === $this->validity_category_list['Annual']) {
                $this->categoryListToReturn[] = 'Annual';
            }
        });
        Log::info('category bundle data providers list', ['data' => $this->categoryListToReturn]);

        $collection->map(function ($item) {
            if (Str::contains($item['data'], 'MTN DATA BUNDLE')) {
                Str::remove('MTN DATA BUNDLE', $item['short_name']);
            }
            $this->bundleMapToReturn[] = ['item_code' => $item['biller_code'], 'data' => $item['short_name'], 'price' => $item['amount'], 'validity_period' => $item['validity_period'].''.'days', 'category' => $this->getCategory($item['validity_period'])];
        })->toArray();

        Log::info('bundle map data providers list', ['data' => $this->bundleMapToReturn]);

        return ['category_list' => collect($this->categoryListToReturn)->unique(), 'bundle_map' => $this->bundleMapToReturn];

    }
}
