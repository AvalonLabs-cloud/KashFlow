<?php

namespace App\Transactions;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\Log;

class CreateOtpRecord
{
    public function execute(array $data , User $user): array
    {
        $otp = data_get($data, 'data.0.otp');
        $medium = data_get($data, 'data.0.medium');
        $reference = data_get($data, 'data.0.reference');
        $expiry = data_get($data, 'data.0.expiry');
        try {
            Log::info('otp record creating');

            $otpRecord = DB::transaction(function () use ($otp, $medium, $reference, $expiry , $user) {

                return Otp::create([
                    // 'account_id' => null,
                    'user_id'    => $user->id,
                    'otp'        => $otp,
                    'medium'     => $medium,
                    'reference'  => $reference,
                    'expiration' => Carbon::parse($expiry),
                ]);
            });

            return [
                'status' => true,
                'data'   => $otpRecord,
            ];
        } catch (Throwable $e) {

            return [
                'status' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }
}
