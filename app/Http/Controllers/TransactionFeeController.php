<?php

namespace App\Http\Controllers;

use App\Service\TransactionFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionFeeController extends Controller
{
    public function __construct(protected TransactionFeeService $transactionFeeService) {}

    public function transactionFee(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $transactionfeeParameters = [
            'amount' => $request->query('amount'),
        ];

        $result = $this->transactionFeeService->transferFee($transactionfeeParameters);
        if ($result['status'] === 'success') {
            return response()->json($result['data']);
        } else {
            Log::info('something unexpected happened', $result);
        }
        Log::info('messsage log ', $result);

        return response()->json($result);
    }
}
