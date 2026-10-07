<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhaseOneOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $onboardingPhase = $user->onboarding?->phase;

            if ($onboardingPhase) {
                return redirect('onboarding/'.$onboardingPhase->value)->with('info', 'Current boarding phase.');
            }
        }

        return $next($request);
    }
}
