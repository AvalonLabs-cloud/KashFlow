<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Transactions\Admin\Services\InitiateTransaction;
use App\Domains\Transactions\Admin\Services\ExcecuteTransaction;
use App\Domains\Transactions\Admin\Services\MarkAsFailed;
use Illuminate\Support\Facades\Log;
use App\Domains\Transactions\Admin\Exceptions\CashoutFailedException;

class AdminCheckoutController extends Controller
{
    public function initiateTransfer(Request $request, InitiateTransaction $initiateTransaction, ExcecuteTransaction $excecuteTransaction, MarkAsFailed $failTransaction)
    {
        try {
            $transaction = $initiateTransaction->execute();
            $payloadForPaymentServiceProvider = [
                'account_number' => $request->user()->account_number,
                'account_bank' => $request->user()->bank_name,
                'amount' => $transaction['transaction']->amount,
                'reference' => $transaction['transaction']->tx_reference,
                'metadata' => [
                    'type' => 'admin'
                ]
            ];

            try {
                $excecuteTransaction->excecute(data: $payloadForPaymentServiceProvider);
                return back()->with([
                    'success' => 'Transfer processing',
                ], 200);
            } catch (CashoutFailedException $e) {

                Log::error('Transfer failed for transaction reference: ' . $e->transactionReference . '. Reason: ' . $e->getMessage());

                $failTransaction->markAsFailed(
                    transactionReference: $e->transactionReference,
                );

                Log::info('error created when trying to create transfer ' . $e->getMessage());
                return back()->with([
                    'error' => 'sorry they was an error completing the transfer',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::info('error created when trying to create transfer ' . $e->getMessage());
            return back()->with([
                'error' => 'sorry they was an error completing the transfer',
            ], 400);
        }
    }
}
