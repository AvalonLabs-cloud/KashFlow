<?php

namespace App\Http\Controllers;

use App\ClientProvider\FlutterwaveClient;
use Illuminate\Http\Request;
use App\Domains\Transactions\Services\InitiateTransaction;
use App\Domains\Transactions\Services\FailTransaction;
use Illuminate\Support\Facades\Log;
use App\Domains\Bills\Airtime\Exceptions\AirtimePurchaseFailedException;
use Inertia\Inertia;
use App\Domains\Bills\Airtime\Services\ExcecuteTransaction;

class AirtimeController extends Controller
{
    public function processAirtime(Request $request, InitiateTransaction $initiateTransaction, ExcecuteTransaction $excecuteTransaction, FailTransaction $failTransaction)
    {
        Log::info('airtime controller hit');
        $payload = [
            'type' => $request->input('transactionType'),
            'amount' => $request->input('amount'),
            'metadata' => [
                'selectedProvider' => $request->input('selectedProvider'),
                'phoneNumber' => $request->input('phoneNumber'),
                'selectedAmount' => $request->input('amount'),
            ],
        ];
        Log::info('biller_code ', $payload);

        try {
            Log::info('airtime controller hit 2 ');
            $transaction = $initiateTransaction->execute(account: auth('web')->user()->account, type: $payload['type'], amount: $payload['amount'], metadata: $payload['metadata']);
            Log::info('airtime controller hit 3');
            $payloadForPaymentServiceProvider = [
                'bill_type' => $transaction['transaction']->type,
                'biller_code' => $transaction['transaction']->metadata['selectedProvider'],
                'customer_id' => $transaction['transaction']->metadata['phoneNumber'],
                'amount' => $transaction['transaction']->amount,
                'reference' => $transaction['transaction']->transaction_reference,
                'metadata' => [
                    'provider_operation_reference' => $transaction['provider_operation']->client_reference,
                ]
            ];

            try {
                Log::info('airtime controller hit 4');
                $excecuteTransaction->excecute(data: $payloadForPaymentServiceProvider);
                Log::info('airtime controller hit 5');
                return redirect()->route('complete_transfer_status', [
                    'transactionReference' => $payloadForPaymentServiceProvider['reference'],
                    'clients_name' => auth('web')->user()->first_name,
                ]);
            } catch (AirtimePurchaseFailedException $e) {

                Log::error('Airtime purchase failed for transaction reference: ' . $e->transactionReference . '. Reason: ', $e->providerResponse);

                // $failTransaction->markAsFailed(
                //     providerOperationReference: $e->providerReference,
                //     failureReason: $e->getMessage(),
                //     transactionReference: $e->transactionReference,
                //     responsePayload: $e->providerResponse,
                // );

                return back()->with([
                    'message' => 'Failed to initiate transaction.',
                    'error' => $e->getMessage(),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::info('airtime error '.$e->getMessage());
            return back()->with([
                'message' => 'Failed to initiate transaction.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
