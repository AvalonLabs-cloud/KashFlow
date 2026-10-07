<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use App\Domains\Transactions\Services\InitiateTransaction;
use App\Domains\Bills\Data\Services\ExcecuteTransaction;
use App\Domains\Bills\Data\Exceptions\DataPurchaseFailedException;
use Illuminate\Support\Facades\Log;
use App\Domains\Transactions\Services\FailTransaction;
use Inertia\Inertia;

class DataBillController extends Controller
{
    public function processDataBill(Request $request, InitiateTransaction $initiateTransaction, ExcecuteTransaction $excecuteTransaction, FailTransaction $failTransaction)
    {
        Log::info('hit bill data point');
        $payload = [
            'type' => $request->input('transactionType'),
            'amount' => Context::get('data_transaction_amount'),
            'metadata' => [
                'billerCode' => Context::get('billerCode'),
                'phoneNumber' => $request->input('phoneNumber'),
                'itemCode' => $request->input('itemCode'),
            ],
        ];

        Log::info('bill paylaod ', $payload);

        try {
            $transaction = $initiateTransaction->execute(account: auth('web')->user()->account, type: $payload['type'], amount: $payload['amount'], metadata: $payload['metadata']);
            $payloadForPaymentServiceProvider = [
                'biller_code' => $transaction['transaction']->metadata['billerCode'],
                'item_code' => $transaction['transaction']->metadata['itemCode'],
                'customer_id' => $transaction['transaction']->metadata['phoneNumber'],
                'amount' => $transaction['transaction']->amount,
                'reference' => $transaction['transaction']->transaction_reference,
                'metadata' => [
                    'provider_operation_reference' => $transaction['provider_operation']->client_reference,
                ]
            ];

            try {
                $excecuteTransaction->excecute(data: $payloadForPaymentServiceProvider);
                return redirect()->route('complete_transfer_status', [
                    'transactionReference' => $payloadForPaymentServiceProvider['reference'],
                    'clients_name' => auth('web')->user()->first_name,
                ]);
            } catch (DataPurchaseFailedException $e) {

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
            return back()->with([
                'message' => 'Failed to initiate transaction.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
