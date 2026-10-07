<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrchestratorTransferService
{
    protected string $baseUrl;

    protected string $clientId;

    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.flutterwave.base_url', 'https://api.flutterwave.com/v4');
        $this->clientId = config('services.flutterwave.client_id');
        $this->clientSecret = config('services.flutterwave.client_secret');
    }

    /**
     * Initiate a bank transfer.
     *
     * * @param array $data [amount, bank_code, account_number, currency, narration, reference]
     */
    public function transfer(array $data): array
    {
        $reference = $data['reference'] ?? 'trf-'.Str::uuid();

        try {
            $token = $this->getAccessToken();

            $payload = [
                'action' => 'instant',
                'type' => 'bank',
                'reference' => $reference,
                'narration' => $data['narration'] ?? 'Service Payout',
                'payment_instruction' => [
                    'amount' => [
                        'value' => (float) $data['amount'],
                        'applies_to' => 'destination_currency',
                    ],
                    'source_currency' => $data['source_currency'] ?? 'NGN',
                    'destination_currency' => $data['currency'] ?? 'NGN',
                    'recipient' => [
                        'type' => 'bank_ngn', // Adjusted based on currency
                        'bank' => [
                            'account_number' => $data['account_number'],
                            'code' => $data['bank_code'],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($token)
                ->withHeaders([
                    'X-Idempotency-Key' => $reference,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/transfers", $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info("Flutterwave Transfer Success: {$reference}", ['data' => $result]);

                return [
                    'status' => 'success',
                    'message' => 'Transfer initiated successfully',
                    'data' => $result['data'],
                ];
            }

            Log::error("Flutterwave Transfer Failed: {$reference}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return [
                'status' => 'failed',
                'message' => $response->json()['message'] ?? 'API Error',
                'data' => $response->json(),
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

    /**
     * Fetch OAuth2 Access Token
     */
    private function getAccessToken(): string
    {
        // In production, cache this token for its 'expires_in' duration (usually 600s)
        return cache()->remember('flw_access_token', 540, function () {
            $response = Http::asForm()->post('https://idp.flutterwave.com/realms/flutterwave/protocol/openid-connect/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->failed()) {
                throw new Exception('Could not authenticate with Flutterwave.');
            }

            return $response->json()['access_token'];
        });
    }
}
