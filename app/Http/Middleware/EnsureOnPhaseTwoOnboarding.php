<?php

namespace App\Http\Middleware;

use App\OnboardingPhase;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnPhaseTwoOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('onboarding/'.OnboardingPhase::PHASE_ONE->value)->with('info', 'Current onboarding phase');

        }

        if (Auth::check() && Auth::user()->onboarding?->phase !== OnboardingPhase::PHASE_TWO) {
            return redirect('onboarding/'.Auth::user()->onboarding->phase->value)->with('info', 'Current onboarding phase');
        }

        return $next($request);

    }
}
