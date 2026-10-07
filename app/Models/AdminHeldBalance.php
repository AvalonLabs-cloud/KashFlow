<?php

namespace App\Models;

use App\AdminHeldBalanceStatus;
use Illuminate\Database\Eloquent\Model;

class AdminHeldBalance extends Model
{
    protected $fillable = [
        'admin_transaction_id',
        'status',
        'amount'
    ];

    protected $casts = [
        'status' => AdminHeldBalanceStatus::class,
    ];
}
