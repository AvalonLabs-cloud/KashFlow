<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompletedOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->user()->onboarding->is_completed) {
            return $next($request);
        }
        // redirect to appropriate route
        // return redirect()->back()->with('message' , 'complete onboarding first');


    }
}
