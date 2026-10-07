<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 use Illuminate\Support\Facades\Log;

class EnsureTransactionTypeIsValid
{
public function handle(Request $request, Closure $next): Response
{
    if (!$request->has('transactionType')) {

        session()->flash('error', 'Something went wrong');

        Log::info('FLASH TEST', [
            'error' => session('error'),
            'session_id' => session()->getId(),
        ]);

        return back();
    }

    if (!in_array(
        $request->input('transactionType'),
        config('AllowedTransactionTypes'),
    )) {

        session()->flash('error', 'Something went wrong');

        Log::info('FLASH TEST', [
            'error' => session('error'),
            'session_id' => session()->getId(),
        ]);

        return back();
    }

    return $next($request);
}
}
