<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
   protected  $table = 'ledger_entries';

    protected $fillable = [
        'user_id',
        'account_id',
        'transaction_id',
        'reference',
        'type',
        'amount',
        'currency',
        'description',
        'metadata',
        'direction',
        'base_currency',
        'ledger_currency',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
    ];

       public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}

