<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAccountIsEligible
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $request->user()->account->checkAccountStatusEligibility();
        } catch (\Exception $e) {
            return back()->with('error', 'Your account is not eligible to perform this transaction.');
        }

           return $next($request);
    }
}
