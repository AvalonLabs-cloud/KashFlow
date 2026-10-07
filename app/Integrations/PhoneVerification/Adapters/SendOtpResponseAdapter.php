<?php

namespace App\Integrations\PhoneVerification\Adapters;

use Illuminate\Support\Facades\Log;

class SendOtpResponseAdapter
{
    public static function parse(mixed $response)
    {
        if ($response !== null) {
            $status = collect($response)->get('status');

            return [
                'status' => $status,
            ];
        } elseif ($response === null) {
            Log::info('null returned');
            return [
                'status' => false,
            ];
        } else {
            Log::info('response from otp', $response);
            return [
                'status' => false,
            ];
        }
    }
}
