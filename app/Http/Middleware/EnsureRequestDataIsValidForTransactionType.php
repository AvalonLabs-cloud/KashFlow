<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Services\DataShapeValueVerificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestDataIsValidForTransactionType
{
    public  function __construct(protected  DataShapeValueVerificationService $verificationService ) {

    }

    public function handle(
        Request $request,
        Closure $next,
    ): Response {

        try {
            $this->verifyDataShapeValue($request,$this->verificationService);
        } catch (\InvalidArgumentException $e) {

            return back()->with('error', 'Invalid request data shape.');
        }

        return $next($request);
    }

    private function verifyDataShapeValue(
        Request $request,
        DataShapeValueVerificationService $verificationService
    ): void {
        switch ($request->input('transactionType')) {
            case 'transfer':
                $verificationService->verifyDataShapeValue(
                    'transfer',
                    $request->all()
                );
                break;

             case 'airtime':
                $verificationService->verifyDataShapeValue(
                    'airtime',
                    $request->all()
                );
                break;

            case 'data':
                $verificationService->verifyDataShapeValue(
                    'data',
                    $request->all()
                );
                break;
        }
    }
}
