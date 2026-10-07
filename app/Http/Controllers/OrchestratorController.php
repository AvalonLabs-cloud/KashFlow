<?php

namespace App\Http\Controllers;

use App\Service\OrchestratorTransferService;
use Illuminate\Http\Request;

class OrchestratorController extends Controller
{
    public function __construct(protected OrchestratorTransferService $flwService) {}

    public function processPayout(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'bank_code' => 'required|string',
            'account_number' => 'required|string|size:10',
        ]);

        // 2. Execute Transfer
        $response = $this->flwService->transfer([
            'amount' => $validated['amount'],
            'bank_code' => $validated['bank_code'],
            'account_number' => $validated['account_number'],
            'narration' => 'Staff Salary Payout',
            'reference' => 'payout-'.time(), // Your unique ID
        ]);

        // 3. Handle UI
        if ($response['status'] === 'success') {
            return response()->json(['message' => 'Payout is being processed.'], 200);
        }

        return response()->json(['error' => $response['message']], 400);
    }
}
