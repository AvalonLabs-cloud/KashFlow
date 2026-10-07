<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAccountPinIsCorrectAndTransactionDataNotTamperedWith
{
    private const SIGNATURE_KEY = 'transaction_signature';

    private const PIN_KEY = 'transaction_pin';

    private const TIMESTAMP_KEY = 'transaction_timestamp';


    private const TIMESTAMP_TTL = 120; // 2 minutes


    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has(self::PIN_KEY)) {
            return $this->verifyTransactionPin($request, $next);
        }

        return $this->requestTransactionPin($request);
    }


    private function requestTransactionPin(Request $request): Response
    {
        if ($request->has(self::SIGNATURE_KEY)) {
            return redirect()->route('payment_pin')->with([
                'transaction_data' => $request->all()
            ]);
        }

        $transactionData = $request->all();

        $transactionData[self::TIMESTAMP_KEY] = now()->timestamp;

        $transactionData[self::SIGNATURE_KEY] = $this->generateSignature(
            $transactionData
        );

        return redirect()
            ->to('/payment_pin')->with([
                'transaction_data' => $transactionData
            ]);
    }

    private function verifyTransactionPin(
        Request $request,
        Closure $next
    ): Response {
        if (!$request->has(self::SIGNATURE_KEY)) {
            return redirect()
                ->back()
                ->withErrors([
                    self::SIGNATURE_KEY => 'Transaction signature is required.',
                ]);
        }

        if (!$request->has(self::TIMESTAMP_KEY)) {
            return redirect()
                ->back()
                ->withErrors([
                    self::TIMESTAMP_KEY => 'Transaction timestamp is required.',
                ]);
        }

        $timestamp = (int) $request->input(self::TIMESTAMP_KEY);

        if ($this->isTimestampExpired($timestamp)) {
            return redirect()
                ->back()
                ->withErrors([
                    self::TIMESTAMP_KEY => 'This transaction authorization has expired.',
                ]);
        }

        $providedSignature = $request->input(self::SIGNATURE_KEY);

        $transactionData = $request->except([
            self::PIN_KEY,
            self::SIGNATURE_KEY,
        ]);

        $expectedSignature = $this->generateSignature($transactionData);

        if (!hash_equals($expectedSignature, $providedSignature)) {
            return redirect()
                ->back()
                ->withErrors([
                    self::SIGNATURE_KEY => 'Invalid transaction signature.',
                ]);
        }

        if (!$this->isTransactionPinCorrect($request)) {
            return redirect()
                ->back()
                ->with([
                 'transaction_data' => $request->except([
                        self::PIN_KEY,
                    ])
                ])
                ->withErrors([
                    'error' => 'Incorrect transaction PIN.',
                ]);
        }

        return $next($request);
    }

    /**
     * Generate an HMAC signature for the transaction payload.
     */
    private function generateSignature(array $transactionData): string
    {
        $canonicalData = $this->canonicalize($transactionData);

        return hash_hmac(
            'sha256',
            $canonicalData,
            config('app.transaction_signature_secret')
        );
    }


    private function canonicalize(array $data): string
    {
        $this->sortRecursively($data);

        return json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    private function sortRecursively(array &$data): void
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->sortRecursively($value);
            }
        }

        if ($this->isAssociativeArray($data)) {
            ksort($data);
        }
    }


    private function isAssociativeArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }


    private function isTimestampExpired(int $timestamp): bool
    {
        $now = now()->timestamp;

        return ($now - $timestamp) > self::TIMESTAMP_TTL;
    }


    private function isTransactionPinCorrect(Request $request): bool
    {
        return Hash::check(
            $request->input(self::PIN_KEY),
            auth('web')->user()->account->pin->pin
        );
    }
}
