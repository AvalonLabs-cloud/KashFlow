<?php

namespace App\Models;

use App\AdminTransactionStatus;
use Illuminate\Database\Eloquent\Model;

class AdminTransaction extends Model
{
    protected $fillable = [
        'admin_id',
        'transaction_id',
        'direction',
        'amount',
        'status',
    ];

    public function holds()
    {
        return $this->hasMany(AdminHeldBalance::class);
    }
    protected $casts = [
       'status' => AdminTransactionStatus::class,
    ];
}
