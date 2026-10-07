<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhaseThreeOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('onboarding/phase_one')->with('info', 'Current onboarding phase');
        }
        if ($request->user()->onboarding?->phase->value !== 'phase_three') {
            return redirect('onboarding/' . $request->user()->onboarding?->phase->value)->with('info', 'Current onboarding phase');
        }
        return $next($request);
    }
}
