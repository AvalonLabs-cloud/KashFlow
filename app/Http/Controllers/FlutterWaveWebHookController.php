<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFlutterwaveWebhook;
use App\Models\WebHookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterWaveWebhookController
{
    public function __invoke(Request $request): Response
    {

        if (!$this->verifySignature($request)) {
            Log::warning('Invalid Flutterwave webhook signature.', [
                'ip' => $request->ip(),
            ]);

            return response()->noContent(401);
        }




        $payload = $request->json()->all();


        if (! is_array($payload) || empty($payload)) {
            return response()->noContent(400);
        }


        $event = $payload['event']
            ?? $payload['type']
            ?? null;

        $eventType = $payload['event.type']
            ?? $payload['event_type']
            ?? null;

        $data = $payload['data']
            ?? [];


        if (!is_array($data)) {
            return response()->noContent(400);
        }

        $eventId = $payload['id'] ?? null;

        if (!$eventId) {

            $eventId = hash(
                'sha256',
                $request->getContent()
            );
        }




        $transactionReference =
            $data['tx_ref']
            ?? $data['reference']
            ?? $data['transaction_reference']
            ?? null;

        $providerReference =
            $data['flw_ref']
            ?? $data['provider_reference']
            ?? null;


        $customer = $data['customer'] ?? [];

        if (!is_array($customer)) {
            $customer = [];
        }


        $amount = $data['amount']
            ?? $data['charged_amount']
            ?? null;

        $currency = $data['currency']
            ?? null;

        $status = $data['status']
            ?? null;

        $paymentType = $data['payment_type']
            ?? $data['payment_method']['type']
            ?? null;




        $serviceType = $this->resolveServiceType(
            event: $event,
            eventType: $eventType,
            data: $data,
        );


        try {
            $webhook = WebHookEvent::firstOrCreate(
                [
                    'event_id' => $eventId,
                ],
                [
                    'event' => $event ?? 'unknown',
                    'event_type' => $eventType,

                    'transaction_reference' =>
                        $transactionReference,

                    'provider_reference' =>
                        $providerReference,

                    'amount' => $amount,
                    'status' => $status,

                    'customer_id' =>
                        $customer['id'] ?? null,

                    'customer_name' =>
                        $customer['name'] ?? null,

                    'customer_email' =>
                        $customer['email'] ?? null,

                    'customer_phone' =>
                        $customer['phone_number']
                        ?? $customer['phone']
                        ?? null,

                    'payment_type' => $paymentType,

                    'service_type' => $serviceType,

                    'payload' => $payload,

                    'metadata' =>
                        $payload['meta_data']
                        ?? $payload['meta']
                        ?? null,

                    'provider_created_at' =>
                        $data['created_at']
                        ?? $data['created_datetime']
                        ?? null,
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {



            if ($this->isDuplicateEventException($e)) {
                return response()->noContent(200);
            }

            throw $e;
        }



        if (!$webhook->wasRecentlyCreated) {
            return response()->noContent(200);
        }


        ProcessFlutterwaveWebhook::dispatch(
            $webhook->id
        );

        return response()->noContent(200);
    }



    private function verifySignature(Request $request): bool
    {
        $signature = $request->header(
            'flutterwave-signature'
        );

        if (!$signature) {
            return false;
        }

        $secret = config(
            'services.flutterwave.webhook_secret' ?? null
        );

        if (!$secret) {
            Log::critical(
                'Flutterwave webhook secret is not configured.'
            );

            return false;
        }

        $expectedSignature = base64_encode(
            hash_hmac(
                'sha256',
                $request->getContent(),
                $secret,
                true
            )
        );

        return hash_equals(
            $expectedSignature,
            $signature
        );
    }




    private function resolveServiceType(
        ?string $event,
        ?string $eventType,
        array $data,
    ): ?string {

        $value = strtolower(
            implode(' ', array_filter([
                $event,
                $eventType,
                $data['payment_type'] ?? null,
                $data['service_type'] ?? null,
            ]))
        );

        return match (true) {

            str_contains($value, 'airtime')
                => 'airtime',

            str_contains($value, 'data')
                => 'data',

            str_contains($value, 'cable')
                || str_contains($value, 'tv')
                || str_contains($value, 'gotv')
                || str_contains($value, 'dstv')
                => 'cable',

            str_contains($value, 'electricity')
                || str_contains($value, 'power')
                => 'electricity',

            str_contains($value, 'bill')
                || str_contains($value, 'billpayment')
                => 'bill',

            str_contains($value, 'bank_transfer')
                => 'transfer',

            default => null,
        };
    }

    private function isDuplicateEventException(
        \Illuminate\Database\QueryException $exception
    ): bool {


        return in_array(
            $exception->errorInfo[1]
                ?? $exception->errorInfo[0]
                ?? null,
            [1062, '23505'],
            true
        );
    }
}
