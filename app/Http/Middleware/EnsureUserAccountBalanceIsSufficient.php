<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAccountBalanceIsSufficient
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $request->user()->account->checkAvailableBalanceEligibility();
        } catch (\Exception $e) {
            return back()->with('error', 'Your account does not have sufficient funds to perform this transaction.');
        }
        return $next($request);
    }
}
