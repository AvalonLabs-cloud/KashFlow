<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class AccessToTransferEndPoint
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $targetUrl = $request->fullUrl();

        session(['intended_url' => $targetUrl]);
        $isTokenValid = $this->verify($request->session('access_token'));

        if (is_null($isTokenValid)) {
            return redirect()->route('pin.index');
        }

        $lastAccess = session('token_created');

        $lastAccessTime = Carbon::parse($lastAccess);
        $now = Carbon::now();

        $diffInSeconds = $lastAccessTime->diffInSeconds($now);

        if ($diffInSeconds > config('flutterwave.pin_token_limit')) {
            return redirect()->route('pin.index');
        }

        session()->forget('accessLimit');
        session()->forget('intended_url');
        session()->forget('access_token');

        return $next($request);
    }

    public function verify($encryptedtoken)
    {
        if (is_null($encryptedtoken)) {
            return null;
        }

        try {
            $decryptedToken = Crypt::decryptString($encryptedtoken);
            $userPin = Auth::user()->pin;
            if ($decryptedToken !== $userPin) {
                return null;
            }

            return true;
        } catch (\Exception $e) {
            return null;
        }
    }
}
