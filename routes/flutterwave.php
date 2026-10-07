<?php

use App\Http\Controllers\FlutterwaveController;
use App\Http\Controllers\OrchestratorController;
use Illuminate\Support\Facades\Route;

// Route::prefix('flutterwave')->group(function () {
//     Route::get('/banks', [FlutterwaveController::class, 'fetchBanks']);
//     Route::post('/accounts/resolve', [FlutterwaveController::class, 'resolveAccount']);
//     Route::post('/processPayout', [OrchestratorController::class, 'processPayout']);
//     Route::post('/airtime', [FlutterwaveController::class, 'processAirtime']);
//     Route::post('/complete-transfer', [FlutterwaveController::class, 'completeTransfer'])->name('complete_transfer');
//     Route::get('/transaction_fees', [FlutterwaveController::class, 'checkTransferFee']);
//     Route::get('/transaction_status_check', [FlutterwaveController::class, 'checkTransferStatus']);
//     Route::get('e/transfers/fee', [FlutterwaveController::class, 'checkFee']);
//     Route::post('/transfers', [FlutterwaveController::class, 'store']);
//     Route::get('/transfers/{id}', [FlutterwaveController::class, 'show']);
//     Route::post('/webhook', [FlutterwaveController::class, 'webhook'])
//         ->name('flutterwave.webhook');
// });
