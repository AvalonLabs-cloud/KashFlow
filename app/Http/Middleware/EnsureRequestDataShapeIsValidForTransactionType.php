<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Services\DataShapeVerificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestDataShapeIsValidForTransactionType
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
                Log::info('shape verification 1 hit');

        try {
            $this->verifyDataShape($request, app(DataShapeVerificationService::class));
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', 'Invalid request data shape');
        }

        return $next($request);
    }

    private function verifyDataShape(
        Request $request,
        DataShapeVerificationService $verificationService
    ): void {
        switch ($request->input('transactionType')) {
            case 'transfer':
                $verificationService->verifyDataShape(
                    'transfer',
                    $request->all()
                );
                break;

            case 'airtime':
                $verificationService->verifyDataShape(
                    'airtime',
                    $request->all()
                );
                break;

            case 'data':
                $verificationService->verifyDataShape(
                    'data',
                    $request->all()
                );
                break;
        }
    }
}
