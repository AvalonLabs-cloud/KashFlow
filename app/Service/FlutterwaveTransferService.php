<?php

declare(strict_types=1);

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service to handle Flutterwave Direct Transfers
 * Documentation: https://developers.flutterwave.com/docs/transfers
 */
class FlutterwaveTransferService
{
    protected string $secretKey;

    protected string $baseUrl;

    protected string $getCustomerUrl;

    public function __construct(protected TokenGenerationService $tokenGenerationService)
    {
        $this->secretKey = config('flutterwave.secret_key');
        $this->baseUrl = 'https://developersandbox-api.flutterwave.com';
        $this->getCustomerUrl = 'https://developersandbox-api.flutterwave.com/banks/account-resolve';
    }

    /**
     * Get list of banks for a specific country
     */
    public function getBanks(string $country = ''): array
    {
        $country = $country ?: config('flutterwave.default_country_to_get_banks');
        $response = $this->client()->get('/banks', [
            'country' => $country,
        ]);

        return $response->json();
    }

    /**
     * Resolve account number to account name
     */
    public function resolveAccount(string $accountNumber, string $accountBank): array
    {

        $response = $this->retriveClient()->post($this->getCustomerUrl, [
            'account' => [
                'code' => $accountBank,
                'number' => $accountNumber,
            ],
            'currency' => 'NGN',
        ]);
        Log::info($response);

        return $response->json();
    }

    /**
     * Fetch transfer fee
     */
    public function getTransferFee(float $amount, string $currency = 'NGN'): array
    {
        $response = $this->client()->get('/transfers/fee', [
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return $response->json();
    }

    /**
     * Initiate a bank transfer
     * Implementation follows Direct Transfer schema
     */
    public function initiateTransfer(array $data): array
    {
        // Reference is required for idempotency
        $payload = [
            'account_bank' => $data['account_bank'],
            'account_number' => $data['account_number'],
            'amount' => $data['amount'],
            'narration' => $data['narration'] ?? 'Transfer',
            'currency' => $data['currency'] ?? 'NGN',
            'reference' => $data['reference'] ?? (string) Str::uuid(),
            'callback_url' => $data['callback_url'] ?? route('flutterwave.webhook'),
            'debit_currency' => $data['debit_currency'] ?? 'NGN',
        ];

        $response = $this->client()->post('/transfers', $payload);

        return $response->json();
    }

    /**
     * Get status of a transfer by ID
     */
    public function verifyTransfer(int $id): array
    {
        $response = $this->client()->get("/transfers/{$id}");

        return $response->json();
    }

    /**
     * Verify Webhook Signature
     */
    public function verifyWebhook(string $signature, string $payload): bool
    {
        $secret = config('services.flutterwave.webhook_hash');

        return $signature === $secret;
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => $this->tokenGenerationService->getAuthHeader(),
            'Content-Type' => 'application/json',
            'X-Trace-Id' => uniqid('', true),
        ])
            ->baseUrl($this->baseUrl);
    }

    protected function retriveClient()
    {
        return Http::withHeaders([
            'Authorization' => $this->tokenGenerationService->getAuthHeader(),
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'X-Trace-Id' => uniqid('', true),
        ]);
    }
}
