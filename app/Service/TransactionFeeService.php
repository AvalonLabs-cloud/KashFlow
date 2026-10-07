<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionFeeService
{
    protected string $baseUrl;

    public function __construct(public TokenGenerationService $generateToken)
    {
        $this->baseUrl = config('flutterwave.base_url');
    }

    /**
     * Initiate a bank transfer.
     *
     * * @param array $data [amount, bank_code, account_number, currency, narration, reference]
     */
    public function transferFee(array $data): array
    {
        $X_Trace_Id = 'trace_'.Str::random(16);
        try {
            $payload = [
                'amount' => $data['amount'],
                'currency' => config('flutterwave.only_currency_unless_the_client_fucking_pays'),
            ];

            $response = Http::withToken(config('flutterwave.flutterwave_secret_key'))
                ->withHeaders([
                    'X-Trace-Id' => $X_Trace_Id,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/transfer/fee", $payload);

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'status' => 'success',
                    'data' => $result['data'],
                ];
            }

            return [
                'status' => 'failed',
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
}
