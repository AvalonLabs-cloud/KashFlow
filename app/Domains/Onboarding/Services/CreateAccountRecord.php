<?php

namespace App\Domains\Onboarding\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

class CreateAccountRecord
{
    public function __construct(public Account $account) {}

    public function execute(mixed $data)
    {
        try {
            $account =  DB::transaction(function () use ($data) {
                $account =  $this->account->create($data);
                $account->user->onboarding->status = 'completed';
                $account->save();
                return $account;
            });
            return $account;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
