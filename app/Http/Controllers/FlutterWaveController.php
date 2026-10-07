<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use App\PreTransactionInitiationProcedure;
use App\Service\FlutterwaveService;
use App\Service\FlutterwaveWebhookManagerService;
use App\Service\TransactionCrudService;
use App\Services\DataBillService\DataBillService;
use App\Utills\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\ClientProvider\FlutterwaveClient;

class FlutterWaveController extends Controller
{
    public function __construct(
        protected FlutterwaveService $flutterwaveService,
        protected FlutterwaveClient $flutterwaveClient,
    ) {}


    public function fetchBanks(Request $request): JsonResponse
    {
        $result = $this->flutterwaveService->getBanks();

        return response()->json($result);
    }

    public function resolveAccount(Request $request): JsonResponse
    {
        $request->validate([
            'accountNumber' => 'required|string',
            'bankCode' => 'required|string',
        ]);

        Log::info('Received account resolution request', [
            'accountNumber' => $request->accountNumber,
            'bankCode' => $request->bankCode,
        ]);
        $result = $this->flutterwaveClient->resolveAccount(
           accountNumber: $request->accountNumber,
           accountBank: $request->bankCode,
           includeAuth: true
        );
        Log::info('resolved account' , $result);

        if ($result['status'] === 'success' ) {
            return response()->json($result);
        }

        return response()->json(['message' => 'failed to resolve account'], 500);
    }



    public function webhook()
    {
        $webhookManager = App::make(FlutterwaveWebhookManagerService::class);

        return $webhookManager->handle(request());
    }
}
