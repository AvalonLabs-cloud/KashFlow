<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionStatus extends Controller
{
    public function checkTransactionStatus(Request $request, string $transactionReference)
    {
        if (!isset($transactionReference)) {
            return;
        }
        $result = Transaction::where('transaction_reference', $transactionReference)->first();
        if (isset($result)) {
            $status = Transaction::where('transaction_reference', $transactionReference)->value('status');
            return response([
                'status' => $status->value
            ]);
        } else {
            return;
        }
    }
}
      