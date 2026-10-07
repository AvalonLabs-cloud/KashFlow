<?php

namespace App\Http\Controllers;

use App\Domains\Wallet\Repository;

use App\Models\User;

class WalletController extends Controller
{
    public function balance(Repository $walletRepository)
    {

        $availableBalance = $walletRepository->AvailableBalance();
        return response()->json(
            [
                'available_balance' => $availableBalance,
            ],
            200
        );
    }
}
