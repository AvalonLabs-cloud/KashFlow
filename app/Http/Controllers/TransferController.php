<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Transactions\Services\InitiateTransaction;
use Inertia\Inertia;
use App\Domains\Transfer\Services\ExcecuteTransaction;
use App\Domains\Transactions\Services\FailTransaction;
use App\Domains\Transfer\Exceptions\TransferFailedException;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    public function initiateTransfer(Request $request, InitiateTransaction $initiateTransaction, ExcecuteTransaction $excecuteTransaction, FailTransaction $failTransaction)
    {
        $payload = [
            'type' => $request->input('transactionType'),
            'amount' => $request->input('amount'),
            'metadata' => [
                'bankCode' => $request->input('bankCode'),
                'accountNumber' => $request->input('accountNumber'),
                'recipientname' => $request->input('recipientname'),
            ],
        ];

        try {
            $transaction = $initiateTransaction->execute(account: auth('web')->user()->account, type: $payload['type'], amount: $payload['amount'], metadata: $payload['metadata']);
            $payloadForPaymentServiceProvider = [
                'account_number' => $transaction['transaction']->metadata['accountNumber'],
                'account_bank' => $transaction['transaction']->metadata['bankCode'],
                'amount' => $transaction['transaction']->amount,
                'recipient_name' => $transaction['transaction']->metadata['recipientname'],
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
            } catch (TransferFailedException $e) {

                Log::error('Transfer failed for transaction reference: ' . $e->transactionReference . '. Reason: ', $e->providerResponse);

                $failTransaction->markAsFailed(
                    providerOperationReference: $e->providerReference,
                    failureReason: $e->getMessage(),
                    transactionReference: $e->transactionReference,
                    responsePayload: $e->providerResponse,
                );

                Log::info('error created when trying to create transfer ' . $e->getMessage());
                return back()->with([
                    'message' => 'Failed to initiate transaction.',
                    'error' => 'sorry they was an error completing the transfer',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::info('error created when trying to create transfer ' . $e->getMessage());
            return back()->with([
                'message' => 'Failed to initiate transaction.',
                'error' => 'sorry they was an error completing the transfer',
            ], 400);
        }
    }
}
