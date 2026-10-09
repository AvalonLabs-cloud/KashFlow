<?php

namespace App\ClientProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FlutterwaveClient
{
    protected  $baseUrl;

    protected array $headers = [];

    public function __construct()
    {
        $this->baseUrl = config('flutterwave.base_url');

        $this->headers = [
            'Accept' => 'application/json',
        ];
    }

    public function get(string $endpoint, array $params = [], bool $includeAuth = false)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.secret_key');
        }

        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . $endpoint, $params)
            ->json();
    }

    public function post(string $endpoint, array $payload = [], bool $includeAuth = false)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        return Http::withHeaders($this->headers)
            ->post($this->baseUrl . $endpoint, $payload)
            ->json();
    }

    public function sendVerificationOtp(string $phone_number, string $endpoint, array $payload = [], bool $includeAuth = false)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        $payload = [
            'length' => config('flutterwave.otp_lenght'),
            'customer' => [
                'name' => Auth::user()->name ?? 'anthony',
                'email' => Auth::user()->email ?? 'tochukwuemmanuel123@gmail.com',
                'phone' => Str::substrReplace($phone_number, '234', 0, 1),
            ],
            'sender' => 'paynow',
            'send' => true,
            'medium' => [
                config('flutterwave.otp_medium'),
            ],
            'expiry' => 1,
        ];

        return Http::withHeaders($this->headers)
            ->post($this->baseUrl . $endpoint, $payload)
            ->json();
    }

    public function verifyVerificationCode(bool $includeAuth = false, string $endpoint = '', array $payload = [])
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }
        $payload = [
            'otp' => $payload['otp'],
        ];

        return Http::withHeaders($this->headers)
            ->post($this->baseUrl . $endpoint, $payload)
            ->json();
    }

    public function initiateBvnVerificationConsent(bool $includeAuth = false, string $endpoint = '', array $payload = [])
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        $payload['redirect_url'] = config('flutterwave.bvn_verification_redirect_url');

        return Http::withHeaders($this->headers)
            ->post($this->baseUrl . $endpoint, $payload)
            ->json();
    }

    public function resolveAccount(string $accountNumber, string $accountBank, bool $includeAuth = false,)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        return  Http::withHeaders($this->headers)
            ->post($this->baseUrl . '/accounts/resolve', [
                'account_number' => '0690000031',
                'account_bank' => '044',
            ])->json();
    }

    public function  executeTransfer(mixed $data, bool $includeAuth = true,)
    {

        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        return  Http::withHeaders($this->headers)
            ->post($this->baseUrl . '/transfers', [
                'account_number' => $data['account_number'],
                'account_bank' => $data['account_bank'],
                'amount' => $data['amount'],
                'reference' => $data['reference'],
                'meta' => $data['metadata'],
            ])->json();
    }


    public function  executeBillPurchase(mixed $data, bool $includeAuth = true,)
    {

        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        return  Http::withHeaders($this->headers)
            ->post($this->baseUrl . '/billers/' . $data['biller_code'] . '/items/' . $data['item_code'] . '/payment', [
                'country' => 'NG',
                'customer_id' => $data['customer_id'],
                'amount' => $data['amount'],
                'reference' => $data['reference'],
                'meta' => $data['metadata'],
            ])->json();
    }



    public function verifyTransactionFromFlutterwave(mixed $tx_ref, bool $includeAuth = true,)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }

        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . '/v3/transactions/verify_by_reference?tx_ref=' . $tx_ref)->json();
    }


    public function getBillProvidersList(string $billType, bool $includeAuth = true)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }


        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . "/bills/{$billType}/billers/?country=NG")->json();
    }


    public function getItemCodesForBill(string $billerCode, bool $includeAuth = true)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
        }


        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . "/billers/{$billerCode}/items")->json();
    }


    public function validateCustomerDetailsForBillPayment(string $itemCode, string $customerId, bool $includeAuth = true)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');;
        }

        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . "/bill-items/{$itemCode}/validate?customer={$customerId}")->json();
    }

    public function createPermanentVirtualAccount(mixed $data, bool $includeAuth = true)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');
            $this->headers['X-Idempotency-Key'] = auth('web')->user()->account_creation_idempotency_key;
            $this->headers['Content-Type'] = 'application/json';
            $this->headers['accept'] = 'application/json';
        }

        return Http::withHeaders($this->headers)
            ->post($this->baseUrl . "/virtual-account-numbers", [
                'email' => $data['email'],
                'bvn' => $data['bvn'],
                'currency' => $data['currency'],
                'amount' => $data['amount'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'tx_ref' => auth('web')->user()->tx_ref,
                'is_permanent' => true,
                'phonenumber' => auth('web')->user()->phone,
                'bank_code' => $data['bank_code'],
                'narraction' => auth('web')->user()->full_name ??  Str::before(auth('web')->user()->email, '@'),
            ])->json();
    }


    public function transactionFee(mixed $amount, bool $includeAuth = true)
    {
        if ($includeAuth) {
            $this->headers['Authorization'] = 'Bearer ' . config('flutterwave.flutterwave_secret_key');;
        }

        return Http::withHeaders($this->headers)
            ->get($this->baseUrl . "/transactions/fee", [
                'amount' => (int)$amount,
                'currency' => 'NGN',
                'payment_type' => 'bank_transfer',
            ])->json();
    }
}
