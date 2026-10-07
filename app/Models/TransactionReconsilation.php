<?php

namespace App\Models;

use App\TransactionReconsilationStatus;
use Illuminate\Database\Eloquent\Model;


class TransactionReconsilation extends Model
{
    protected $fillable = [
        'transaction_id',
        'reconsilation_time',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    protected function casts(): array
    {
        return [
            'status' => TransactionReconsilationStatus::class,
            'reconsilation_time' => 'datetime',
        ];
    }
}
