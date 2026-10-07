<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('account-balance.{id}', function (User $user,int $id) {
    return (int) $user->id === (int) $id;
});

