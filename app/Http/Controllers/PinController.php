<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PinController extends Controller
{
    public function pinUi()
    {
        return Inertia::render('pin/SetPin');
    }

    public function setPin(Request $request)
    {
        $pin = $request->user('web')?->account?->pin;
        if (is_null($pin)) {
            $request->user()->account->pin()->create([
                'pin' =>  $request->input('pin'),
            ]);
        }
        return redirect('/main-index');
    }
}
