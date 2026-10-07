<?php

namespace App\Http\Controllers;

use App\Domains\Onboarding\Actions\sendPhoneNumberVerificationOtp;
use App\Domains\Onboarding\Actions\verifyPhoneNumberVerificationOtp;
use App\Integrations\PhoneVerification\Contracts\PhoneNumberVerification as PhoneNumberVerificationContract;
use App\Integrations\PhoneVerification\Flutterwave\FlutterWavePhoneVerification;
use App\Models\User;
use App\OnboardingPhase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Domains\Onboarding\Actions\initiateConsentForBvnVerification;
use App\BvnConsentStatus;
use Illuminate\Support\Str;
use App\Domains\Onboarding\Services\CompleteOnboarding;

class OnboardingController extends Controller
{
    public function showPhaseOne()
    {
        return Inertia::render('onboarding/PhaseOne');
    }

    public function storePhaseone(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        $user->onboarding()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'phase' => OnboardingPhase::PHASE_TWO,
            'is_authenticated' => true,
        ]);

        event(new Registered($user));

        return redirect('onboarding/phase_two')->with('success', 'Phase one completed successfully');
    }

    public function showPhasetwo(Request $request)
    {
        return Inertia::render('onboarding/PhaseTwo');
    }

    public function sendPhoneNumberOtp(Request $request, sendPhoneNumberVerificationOtp $phoneNumberVerification)
    {
        $request->validate([
            'phone_number' => 'required|string|size:11',
        ]);
        $user = Auth::user();
        $verificationStatus = $phoneNumberVerification->execute(phone_number: $request->phone_number, user: $request->user(), registerPhoneNumber: true);

        if (app(PhoneNumberVerificationContract::class) instanceof FlutterWavePhoneVerification) {
            if ($verificationStatus['status']) {
                // $request->user()->account()->otps()->create([
                //     'otp' => $otpData['otp'],
                //     'medium' => $otpData['medium'],
                //     'reference' => $otpData['reference'],
                //     'expiration' => $otpData['expiration'],
                // ]);

                return redirect()->route('confirm.phone.otp')->with('success', 'Verification code sent successfully');
            } else {
                Log::error('Failed to create OTP record for user', [
                    'user_id' => $user->id,
                    'phone_number' => $request->phone_number,
                ]);

                return back()->with('error', 'Failed to send verification code. Please try again.');
            }
        }
    }


    public function showConfirmPhoneOtp(Request $request)
    {
        return Inertia::render('onboarding/ConfirmPhoneOtp');
    }


    public function verifyPhoneNumberOtp(Request $request, verifyPhoneNumberVerificationOtp $verifyPhoneVerificationOtp)
    {

        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        if (app()->environment('local')) {
            return redirect()->route('bvn.consent');
        }

        $user = $request->user();

        $verificationResult = $verifyPhoneVerificationOtp->execute(verification_code: $request->verification_code, user: $user);

        if ($verificationResult['status']) {
            return redirect()->route('bvn.consent')->with('success', 'Phone number verified successfully');
        } else {
            return back()->with('error', $verificationResult['message'] ?? 'Failed to verify phone number. Please try again.');
        }
    }


    public function resendPhoneVerificationOtp(Request $request, sendPhoneNumberVerificationOtp $phoneNumberVerification)
    {
        $phoneNumberVerification->execute(user: $request->user(), registerPhoneNumber: false, phone_number: '');
    }


    public function showPhaseThree(Request $request)
    {
        return Inertia::render('onboarding/PhaseThree');
    }

    public function storeBvn(Request $request)
    {
        $request->validate([
            'bvn' => ['required', 'size:11'],
        ]);

        $user = $request->user();

        if (is_null($user->bvn)) {
            $user->update([
                'bvn' => $request->bvn,
            ]);
        }

        $user->onboarding()->update([
            'phase' => OnboardingPhase::PHASE_FOUR,
        ]);

        // Continue to the full-name step.
        return redirect()->route('full.name');
    }


    public function showPhaseFour(Request $request)
    {
        return Inertia::render('onboarding/PhaseFour');
    }


    public function showConsentResult(Request $request)
    {
        return Inertia::render('onboarding/BvnConsentStatus');
    }

    public function showPhaseFive(Request $request)
    {
        return Inertia::render('onboarding/PhaseFive');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'fullName' => 'sometimes|required|string',
        ]);

        if ($request->post('fullName')) {
            $request->user()->update([
                'full_name' => $request->post('fullName'),
            ]);
        }

        $request->user()->update([
            'tx_ref' => 'TXN-' . strtoupper(Str::random(16)),
            'account_creation_idempotency_key' => 'ACC-' . strtoupper(Str::random(16)),
        ]);

        $dataToCreateVirtualAccount = [
            'email' => $request->user()->email,
            'currency' => 'NGN',
            'amount' => 0,
            'firstname' => $request->user()->first_name ?? '',
            'lastname' => $request->user()->last_name ?? '',
            'is_permanent' => true,
            'bvn' => $request->user()->bvn,
            'tx_ref' => $request->user()->tx_ref,
            'phonenumber' => $request->user()->phone,
            'bank_code' => config('flutterwave.IndulgeMFB'),
        ];

        $completeOnboardingService = app(CompleteOnboarding::class);
        $result = $completeOnboardingService->execute($dataToCreateVirtualAccount);
        if ($result) {
           return redirect()->route('pin.set');
        }
       return redirect()->back()->with('onboarding_completion_error' , 'something went wrong try again later');
    }
}
