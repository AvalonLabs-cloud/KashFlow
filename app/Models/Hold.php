<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\HoldStatus;

class Hold extends Model
{
    protected $table = 'holds';

    protected $fillable = [
        'account_id',
        'transaction_id',
        'user_id',
        'amount',
        'status',
        'base_currency',
        'ledger_currency',
        'held_at',
        'released_at',
        'consumed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }



    protected $casts = [
        'status' => HoldStatus::class,
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];
}
