<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecoveryPin extends Model
{
    protected $fillable = [
       'account_id',
       'token_hash',
       'expires_at',
       'verified_at'
    ];
}
