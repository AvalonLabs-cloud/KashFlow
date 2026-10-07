<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'sender_id',
        'amount',
        'recipient_account_number',
        'recipient_bank_code',
        'status',
        'identity',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }
}
