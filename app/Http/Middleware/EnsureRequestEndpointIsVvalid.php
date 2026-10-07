<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestEndpointIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $response = $this->ensureTransactionEndpointIsValid($request);
    }

    private function ensureTransactionEndpointIsValid(Request $request)
    {
        $transactionType = $request->input('transactionType');

        $validEndpoints = [
            'airtime'  => 'transaction/airtime',
            'data'     => 'transaction/data',
            'transfer' => 'transaction/transfer',
        ];

        if (!isset($validEndpoints[$transactionType])) {
            return redirect()
                ->back()
                ->with('error' , 'invalid transaction type');
        }

        $expectedEndpoint = $validEndpoints[$transactionType];

        if (!$request->is($expectedEndpoint)) {
            return redirect()
                ->back()
                ->withErrors([
                    'transactionType' =>
                        "The transaction type '{$transactionType}' cannot be processed through this endpoint.",
                ]);
        }
    }
}
