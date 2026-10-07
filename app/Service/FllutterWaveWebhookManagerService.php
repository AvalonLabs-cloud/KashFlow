<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class FlutterwaveWebhookManagerService
{
    public function handle(Request $request)
    {
        $payload = $request->json();
        $requestBody = $request->getContent();
        $signature = $request->header('flutterwave-signature');
        $result = $this->verifySignature($requestBody, $signature);
        if (! $result) {
            Log::error('Invalid Flutterwave webhook signature');

            return [
                'status' => 'error',
                'message' => 'Invalid signature',
            ];
        }

        match ($payload['event']) {
            'charge.completed' => 'handleChargeCompleted',
            'transfer.completed' => 'handleTransferCompleted',
            default => 'ignore',
        };
    }

    public function verifySignature(mixed $requestBody, mixed $receivedSignature): bool
    {
        $secret = config('services.flutterwave.secret_hash');
        $computed = hash_hmac('sha256', $requestBody, $secret);

        return hash_equals($receivedSignature, $computed);
    }
}
