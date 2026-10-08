<?php

namespace App\Service;

use App\Jobs\CheckAndUpdateTransferStatus;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveService
{
    protected  $baseUrl;

    protected $secretKey;

    protected $accessToken;

    protected $tokenGenerationUrl;

    protected $clientId;

    protected $customer_id;

    protected $userReference;

    protected $transactionretry = 3;

    protected $lastTokenGenerationTime;

    protected $accountNumber;

    protected $bankName;

    protected $bankNumber;

    protected $referenceToStore;

    protected $customerIdToStore;

    protected $accountIdToStore;

    protected $user;

    public $userInfo = [];

    protected $secretFlutterwaveKey;

    public function __construct()
    {
        $this->baseUrl = config('flutterwave.base_url' , 'https://api.flutterwave.com/v3');
        $this->secretKey = config('flutterwave.secret_key');
        $this->tokenGenerationUrl = config('flutterwave.token_generation_url');
        $this->clientId = config('flutterwave.client_id');
        $this->userReference = Str::random(16);
        $this->accessToken = '';

        $this->secretFlutterwaveKey = config('flutterwave.flutterwave_secret_key');
    }

    public function createIdempotencyKey(): string
    {
        return uniqid('flutterwave_', true);
    }

    public function createRecords($data, $userInfo)
    {
        try {
            $user = DB::transaction(function () use ($data, $userInfo) {
                $user = User::create([
                    'name' => $userInfo['firstname'].' '.$userInfo['lastname'],
                    'email' => $userInfo['email'],
                    'password' => $userInfo['password'],
                    'phone' => isset($userInfo['phone']) ? $userInfo['phone'] : null,
                    'pin' => isset($userInfo['pin']) ? $userInfo['pin'] : null,
                ]);

                $account = Account::create([
                    'user_id' => $user->id,
                    'reference' => $data['flw_ref'],
                    'account_number' => $data['account_number'],
                    'account_name' => $user->name,
                    'bank_name' => $data['bank_name'],
                    'currency' => 'NGN',
                    'account_type' => 'static',
                    'status' => $data['account_status'],
                    // 'flw_account_id' => $this->accountIdToStore,
                ]);

                return $user;
            }, $this->transactionretry);

            return $user;
        } catch (\Throwable $e) {
            Log::info('Transaction failed: '.$e->getMessage());

            return false;
        }
    }

    public function createFlutterwaveVirtualAccount()
    {
        $this->userInfo['firstname'] = config('flutterwave.test_user_info.firstname');
        $this->userInfo['lastname'] = config('flutterwave.test_user_info.lastname');
        $this->userInfo['phone'] = config('flutterwave.test_user_info.phone');
        $this->userInfo['email'] = config('flutterwave.test_user_info.email');
        $this->userInfo['password'] = config('flutterwave.test_user_info.password');
        $this->userInfo['pin'] = '1234';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->secretFlutterwaveKey,
            'X-Idempotency-Key' => $this->createIdempotencyKey(),
        ])->post($this->baseUrl.'/virtual-account-numbers', [
            'email' => 'testtest@gmail.com',
            'amount' => 100,
            'tx_ref' => 'Sample_tx_ref-002',
            'phonenumber' => '08100000000',
            'firstname' => 'Flutterwave',
            'lastname' => 'Developers',
            'narration' => config('flutterwave.default_narration'),
            'is_permanent' => config('flutterwave.type_of_accounts'),
            'bvn' => config('flutterwave.bvn'),
        ]);

        $response = $response->json();
        Log::info('flutterwave pvs response', $response);
        if ($response['status'] === 'success') {
            $record = $this->createRecords($response['data'], $this->userInfo);
            if ($record) {
                return [
                    'status' => true,
                    'message' => 'user create successfully',
                    'user' => $record,
                ];
            }

            return [
                'status' => false,
                'message' => 'failed to create an account',
            ];
        } else {
            Log::info($response);

            return [
                'status' => false,
                'message' => 'failed to create bank account',
            ];
        }
    }

    public function getBanks(string $country = 'NG'): array
    {
        $country = $country ?: config('flutterwave.default_country_to_get_banks');
        $response = Http::withToken($this->secretFlutterwaveKey)
            ->get($this->baseUrl."/banks/{$country}");
        Log::info('bankList '.$response);

        return $response->json();
    }

    // public function resolveAccount($accountNumber, $accountBank)
    // {
    //     $response = Http::withToken($this->secretFlutterwaveKey)
    //         ->withHeaders([
    //             'Content-Type' => 'application/json',
    //         ])
    //         ->post($this->baseUrl.'/accounts/resolve', [
    //             'account_number' => '0690000031',
    //             'account_bank' => '044',
    //         ]);
    //     $response = $response->json();

    //     if ($response['status'] === 'success') {
    //         return [
    //             'status' => true,
    //             'data' => $response['data'],
    //         ];
    //     } else {
    //         return [
    //             'status' => false,
    //             'message' => 'failed to resolve account',
    //         ];
    //     }
    // }

    public function getTransferFee($data)
    {
        try {
            $payload = [
                'amount' => $data,
                'currency' => config('flutterwave.only_currency_unless_the_client_fucking_pays'),
            ];

            $response = Http::withToken($this->secretFlutterwaveKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl.'/transfers/fee', $payload);

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'status' => 'success',
                    'data' => $result['data'],
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'failed to retrieve fees',
            ];
        } catch (Exception $e) {
            Log::critical('Flutterwave Service Exception: '.$e->getMessage());

            return [
                'status' => 'failed',
                'message' => 'A technical error occurred.',
                'data' => [],
            ];
        }
    }

    public function completeTransfer($transferData, $amount, $pin)
    {
        if (App::environment('local')) {
            $response = [
                'status' => 'success',
                'message' => 'Transfer Queued Successfully',
                'data' => [
                    'id' => 26251,
                    'account_number' => '1234567840',
                    'bank_code' => '044',
                    'full_name' => 'Flutterwave Developers',
                    'created_at' => '2020-01-20T16:09:34.000Z',
                    'currency' => 'NGN',
                    'debit_currency' => 'NGN',
                    'amount' => 5500,
                    'fee' => 45,
                    'status' => 'NEW',
                    'reference' => 'akhlm-pstmnpyt-rfxx007_PMCKDU_1',
                    'meta' => null,
                    'narration' => 'Akhlm Pstmn Trnsfr xx007',
                    'complete_message' => '',
                    'requires_approval' => 0,
                    'is_approved' => 1,
                    'bank_name' => 'ACCESS BANK NIGERIA',
                ],
            ];

            return $response;

        }

        // $reference_for_transaction = Str::random(16);
        // try {
        //     $payload = [
        //         'account_bank' => $transferData['account_bank'],
        //         'account_number' => $transferData['account_number'],
        //         'narration' => config('flutterwave.default_narration'),
        //         'currency' => config('flutterwave.only_currency_unless_the_client_fucking_pays'),
        //         'reference' => $reference_for_transaction,
        //         'debit_currency' => config('flutterwave.only_currency_unless_the_client_fucking_pays'),
        //         'amount' => $amount,
        //     ];

        //     $response = Http::withToken($this->secretFlutterwaveKey)
        //         ->withHeaders([
        //             'Accept' => 'application/json',
        //             'Content-Type' => 'application/json',
        //         ])
        //         ->post($this->baseUrl.'/transfers', $payload);

        //     if ($response->successful()) {
        //         $result = $response->json();
        //         $transferDetails = [
        //             'recipient_account_number' => $payload['account_number'],
        //             'recipient_bank_code' => $payload['account_bank'],
        //             'amount' => $payload['amount'],
        //             'status' => 'pending',
        //             'reference' => $payload['reference'],
        //         ];
        //         $transferIdFltServer = $result['data']['id'];
        //         $transferRecordId = $this->createTransferRecordAndCreateQueue($transferDetails, $transferIdFltServer);

        //         return [
        //             'status' => 'success',
        //             'tranferId' => $transferRecordId->identity,
        //             'message' => 'Transfer initiated successfully',
        //         ];

        //     }

        //     return [
        //         'status' => 'failed',
        //         'message' => 'failed to complete transfer',
        //     ];
        // } catch (Exception $e) {
        //     Log::critical('Flutterwave Service Exception: '.$e->getMessage());

        //     return [
        //         'status' => 'failed',
        //         'message' => 'A technical error occurred.',
        //         'data' => [],
        //     ];
        // }
    }

    public function createTransferRecordAndCreateQueue($transferDetails, $transferIdFltServer)
    {
        $transferRecord = DB::transaction(function () use ($transferDetails, $transferIdFltServer) {
            $record = Transfer::create([
                'sender_id' => auth()->id,
                'amount' => $transferDetails['amount'],
                'recipient_account_number' => $transferDetails['recipient_account_number'],
                'recipient_bank_code' => $transferDetails['recipient_bank_code'],
                'status' => $transferDetails['status'],
                'reference' => $transferDetails['reference'],
            ]);

            CheckAndUpdateTransferStatus::dispatch($record->id, $transferIdFltServer)->delay(now()->addSeconds(2));

            return $record;
        });

        return $transferRecord;
    }

    // public function RetrieveAirtimeProviders()
    // {
    //     $bill_list = $this->getBillsList();
    //     if ($bill_list['status'] === 'success') {
    //         $airtime_provider_object = collect($bill_list['data'])->firstWhere('name', 'Airtime');
    //         $airtime_provider_code = $airtime_provider_object['code'];
    //         $airtime_providers_list = $this->getAirtimeProvidersList($airtime_provider_code);
    //         if ($airtime_providers_list['status'] === 'success') {
    //             $airtime_providers_list_collection = collect($airtime_providers_list['data']);
    //             $code_provider_list = $airtime_providers_list_collection->mapWithKeys(function ($item) {
    //                 $item_name = Str::remove('NIGERIA', $item['name']);

    //                 return [$item['biller_code'] => $item_name];
    //             });

    //         }
    //     }

    //     return $code_provider_list ?? [];
    // }

    // public function getBillsList()
    // {
    //     try {
    //         $response = Http::withToken($this->secretFlutterwaveKey)
    //             ->withHeaders([
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //             ])
    //             ->get($this->baseUrl.'/top-bill-categories');

    //         if ($response->successful()) {
    //             $result = $response->json();

    //             return [
    //                 'status' => 'success',
    //                 'data' => $result['data'],
    //             ];
    //         }

    //         return [
    //             'status' => 'failed',
    //             'message' => 'failed to retrieve bills list',
    //         ];
    //     } catch (Exception $e) {
    //         Log::critical('Flutterwave Service Exception: '.$e->getMessage());

    //         return [
    //             'status' => 'failed',
    //             'message' => 'A technical error occurred.',
    //             'data' => [],
    //         ];
    //     }
    // }

    // public function getAirtimeProvidersList(string $airtime_provider_code)
    // {
    //     try {
    //         $response = Http::withToken($this->secretFlutterwaveKey)
    //             ->withHeaders([
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //             ])
    //             ->get($this->baseUrl."/bills/{$airtime_provider_code}/billers/?country=NG");

    //         if ($response->successful()) {
    //             $result = $response->json();

    //             return [
    //                 'status' => 'success',
    //                 'data' => $result['data'],
    //             ];
    //         }

    //         return [
    //             'status' => 'failed',
    //             'message' => 'failed to retrieve airtime providers list',
    //         ];
    //     } catch (Exception $e) {
    //         Log::critical('Flutterwave Service Exception: '.$e->getMessage());

    //         return [
    //             'status' => 'failed',
    //             'message' => 'A technical error occurred.',
    //             'data' => [],
    //         ];
    //     }
    // }

    // public function requestAirtimeCharge(?int $phoneNumber, ?string $network, ?int $amount, string $transactionReference)
    // {
    //     $item_code = $this->getItemCode($network);
    //     if ($item_code['status'] === 'ok') {
    //         $item_code_to_use = $item_code['item_code'];
    //         $status = $this->completeAirtimeCharge($network, $item_code_to_use, $phoneNumber, $amount, $transactionReference);
    //         if ($status['status'] === 'success') {
    //             Log::info('airtime charge request successful', [
    //                 'phone_number' => $phoneNumber,
    //                 'network' => $network,
    //                 'amount' => $amount,
    //                 'transaction_reference' => $transactionReference,
    //                 'data' => $status['data'],
    //             ]);

    //             return [
    //                 'status' => 'success',
    //                 'reference' => $transactionReference,
    //             ];
    //         } else {
    //             Log::info('airtime charge failed', $status);

    //             return [
    //                 'status' => 'failed',
    //                 'message' => 'Failed to complete airtime charge',
    //             ];
    //         }
    //     } else {
    //         Log::error('something failed in the final requestAirtimeCharge method', $item_code);

    //         return [
    //             'status' => 'failed',
    //             'message' => 'Failed to retrieve item code for airtime charge',
    //         ];
    //     }

    // }

    // public function getItemCode(mixed $network)
    // {
    //     try {
    //         $response = Http::withToken($this->secretFlutterwaveKey)
    //             ->withHeaders([
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //             ])
    //             ->get($this->baseUrl."/billers/{$network}/items");

    //         if ($response->successful()) {
    //             $result = $response->json();
    //             Log::info($result['data'][0]['item_code']);

    //             return [
    //                 'status' => 'ok',
    //                 'data' => $result['data'],
    //                 'item_code' => $result['data'][0]['item_code'] ?? null,
    //             ];
    //         }

    //         return [
    //             'status' => 'failed',
    //             'message' => 'failed to retrieve bills list',
    //         ];
    //     } catch (Exception $e) {
    //         Log::critical('Flutterwave Service Exception: '.$e->getMessage());

    //         return [
    //             'status' => 'failed',
    //             'message' => 'A technical error occurred.',
    //             'data' => [],
    //         ];
    //     }

    // }

    // public function completeAirtimeCharge(mixed $biller_code, mixed $item_code, mixed $phone_number, mixed $amount, mixed $transaction_reference)
    // {
    //     // i have no idea why the reference retuned is not the same i sent
    //     try {
    //         $response = Http::withToken($this->secretFlutterwaveKey)
    //             ->withHeaders([
    //                 'Accept' => 'application/json',
    //                 'Content-Type' => 'application/json',
    //             ])
    //             ->post($this->baseUrl."/billers/{$biller_code}/items/{$item_code}/payment", [
    //                 'country' => 'NG',
    //                 'customer_id' => (string) $phone_number,
    //                 'reference' => $transaction_reference,
    //                 'amount' => $amount,
    //                 'callback_url' => config('flutterwave.webhooksite'),
    //             ]);

    //         if ($response->successful()) {
    //             $result = $response->json();

    //             return [
    //                 'status' => 'success',
    //                 'data' => $result['data'],
    //             ];
    //         }

    //         return [
    //             'status' => 'failed',
    //             'message' => $response,
    //         ];
    //     } catch (Exception $e) {
    //         Log::critical('Flutterwave Service Exception: '.$e->getMessage());

    //         return [
    //             'status' => 'failed',
    //             'message' => 'A technical error occurred.',
    //             'data' => [],
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'message' => 'Airtime charge completed successfully',
    //     ];
    // }

    public function retrieveBillerPackages(mixed $biller_code)
    {
        try {
            $response = Http::withToken($this->secretFlutterwaveKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($this->baseUrl."/billers/{$biller_code}/items");

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'status' => 'success',
                    'data' => $result['data'],
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'failed to retrieve bills list',
            ];
        } catch (Exception $e) {
            Log::critical('Flutterwave Service Exception: '.$e->getMessage());

            return [
                'status' => 'failed',
                'message' => 'A technical error occurred.',
                'data' => [],
            ];
        }
    }

    public function webhook() {}
}
