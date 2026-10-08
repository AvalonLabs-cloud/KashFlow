<?php

use App\Http\Controllers\FlutterWaveController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\DataBillController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\OnboardingPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\OrchestratorController;
use App\Http\Controllers\AdminCheckoutController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureTransactionTypeIsValid;
use App\Http\Middleware\EnsureRequestDataShapeIsValidForTransactionType;
use App\Http\Middleware\EnsureRequestDataIsValidForTransactionType;
use App\Http\Middleware\EnsureUserAccountIsEligible;
use App\Http\Middleware\EnsureUserAccountBalanceIsSufficient;
use App\Http\Middleware\EnsureUserAccountPinIsCorrectAndTransactionDataNotTamperedWith;
use App\Http\Middleware\EnsureRequestEndpointIsValid;
use App\Http\Controllers\FlutterWaveWebhookController;
use App\Http\Controllers\PinController;
use App\Http\Middleware\EnsurePhaseOneOnboarding;
use Inertia\Inertia;
use  App\Http\Middleware\EnsureUserHasCompletedOnboarding;
use App\Http\Controllers\TransactionStatus;
use App\Http\Middleware\EnsurePhaseThreeOnboarding;
use App\Http\Middleware\EnsurePhaseFourOnboarding;
use App\Http\Middleware\EnsureOnPhaseTwoOnboarding;



Route::get('/login', [UserController::class, 'loginPage'])->name('login');
Route::post('/login', [UserController::class, 'login']);

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    try {
        $request->fulfill();
        $user = $request->user();
        if ($user) {
            $user->onboarding()->update([
                'phase' => OnboardingPhase::PHASE_THREE,
            ]);
        }

        return redirect()->route('phone.verification.ui');
    } catch (Exception $e) {
        Log::error('Error during email verification', ['error' => $e->getMessage()]);
        return redirect('/onboarding/phase_two')->with('error', 'There was an error verifying your email. Please try again.');
    }
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::prefix('onboarding')->group(function () {
    Route::middleware([EnsurePhaseOneOnboarding::class])->group(function () {
        Route::get('/phase_one', [OnboardingController::class, 'showPhaseone'])->name('phase_one');
        Route::post('/phase_one', [OnboardingController::class, 'storePhaseone']);
    });

    Route::middleware([EnsureOnPhaseTwoOnboarding::class])->group(function () {
        Route::get('/phase_two', [OnboardingController::class, 'showPhasetwo'])->name('verification.notice');


        Route::post('/email/verification-notification', function (Request $request) {
            Log::info('Received request to resend email verification notification');
            $request->user('web')->sendEmailVerificationNotification();
            return back()->with('message', 'Verification link sent!');
        })->middleware(['throttle:10,1'])->name('verification.send');
    });

    Route::middleware(['auth', 'verified', EnsurePhaseThreeOnboarding::class])->group(function () {
        Route::get('/phase_three', [OnboardingController::class, 'showPhaseThree'])->name('phone.verification.ui');
        Route::post('/resend_phone_verification_sms', [OnboardingController::class, 'resendPhoneVerificationOtp']);
        Route::post('/send_phone_verification_sms', [OnboardingController::class, 'sendPhoneNumberOtp']);
        Route::post('/confirm_phone_verification_sms', [OnboardingController::class, 'verifyPhoneNumberOtp']);
        Route::get('/confirm_phone_otp', [OnboardingController::class, 'showConfirmPhoneOtp'])->name('confirm.phone.otp');
    });


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/phase_four', [OnboardingController::class, 'showPhaseFour'])->name('bvn.consent');
        Route::post('/bvn/acquisition', [OnboardingController::class, 'storeBvn'])->name('bvn.consent.initiate');
    });


    Route::get('/phase_five', function () {
        return Inertia::render('onboarding/PhaseFive');
    })->name('full.name');

    Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
});

Route::middleware(['auth', 'verified', EnsureUserHasCompletedOnboarding::class])->group(function () {
    Route::get('/transaction_status', [UserController::class, 'transactionStatus'])->name('complete_transfer_status');
    Route::get('/main-index', [UserController::class, 'indexPage'])->name('index-page');
    Route::get('/airtime', [UserController::class, 'airtimePage']);
    Route::get('/client_profile', [UserController::class, 'clientProfilePage'])->name('profile-page');
    Route::get('/edit-profile', [UserController::class, 'clientProfilePageEdit'])->name('profile-page-edit');
    Route::patch('/edit-profile', [UserController::class, 'clientProfileEdit']);
    Route::get('/data', [UserController::class, 'dataPage'])->name('data');
    Route::get('/data_items', [UserController::class, 'dataBundles']);
    Route::post('/submitComplaint', [UserController::class, 'submitComplaint']);
    Route::get('/payment_pin', [UserController::class, 'paymentPinPage'])->middleware([])->name('payment_pin');
    Route::get('/set_pin', [PinController::class, 'pinUi'])->name('pin.set');
    Route::post('/set_pin', [PinController::class, 'setPin']);
    Route::get('/transaction_detail', [UserController::class, 'transactionDetail']);
    Route::get('/helpDesk', [UserController::class, 'helpDesk']);
    Route::get('/transfer', [UserController::class, 'transferPage'])->name('transfer');
    Route::get('/transfer-amount', [UserController::class, 'transferAmountPage']);
    Route::get('/pin', [UserController::class, 'pin'])->name('pin.index');
    Route::get('/transfer-confirmation', [UserController::class, 'confirmationPage'])->name('transfer-confirmation');
    Route::get('/complete_transfer', [UserController::class, 'completeTransferPage']);
    Route::get('/otp', [UserController::class, 'otpPage'])->name('otp.index');
    Route::get('/balance', [WalletController::class, 'balance']);
    Route::get('/history', [UserController::class, 'history']);
    Route::get('/transactions/{transactionReference}/status', [TransactionStatus::class, 'checkTransactionStatus']);
    Route::get('/cardInfo', [UserController::class, 'cardInfo']);
    Route::get('/transaction-fee' , [UserController::class , 'transactionFee']);
});


Route::post('/admin/cashout',[AdminCheckoutController::class , 'initiateTransfer'])->middleware(['auth','verified' , EnsureIsAdmin::class]);


Route::middleware([
    'auth',
    'verified',
    EnsureTransactionTypeIsValid::class,
    EnsureRequestDataShapeIsValidForTransactionType::class,
    EnsureRequestDataIsValidForTransactionType::class,
    EnsureUserAccountIsEligible::class,
    EnsureUserAccountBalanceIsSufficient::class,
    EnsureUserAccountPinIsCorrectAndTransactionDataNotTamperedWith::class
])->prefix('transaction')->group(function () {
    Route::post('/transfer', [TransferController::class, 'initiateTransfer'])->name('transaction.transfer');
    Route::post('/airtime ', [AirtimeController::class, 'processAirtime'])->name('transaction.airtime');
    Route::post('/data ', [DataBillController::class, 'processDataBill'])->name('transaction.data');
});


// Route::post('/webhook', FlutterWaveWebhookController::class)->name('flutterwave.webhook');


Route::middleware(['auth', 'verified', EnsureUserHasCompletedOnboarding::class])->prefix('flutterwave')->group(function () {
    Route::get('/banks', [FlutterwaveController::class, 'fetchBanks']);
    Route::post('/accounts/resolve', [FlutterwaveController::class, 'resolveAccount']);
    Route::post('/processPayout', [OrchestratorController::class, 'processPayout']);
    Route::post('/airtime', [FlutterwaveController::class, 'processAirtime']);
    Route::post('/complete-transfer', [FlutterwaveController::class, 'completeTransfer'])->name('complete_transfer');
    Route::get('/transaction_fees', [FlutterwaveController::class, 'checkTransferFee']);
    Route::get('/transaction_status_check', [FlutterwaveController::class, 'checkTransferStatus']);
    Route::get('e/transfers/fee', [FlutterwaveController::class, 'checkFee']);
    Route::post('/transfers', [FlutterwaveController::class, 'store']);
    Route::get('/transfers/{id}', [FlutterwaveController::class, 'show']);
    Route::post('/webhook', [FlutterwaveController::class, 'webhook'])
        ->name('flutterwave.webhook');
});



// require __DIR__ . '/flutterwave.php';
require __DIR__ . '/settings.php';
