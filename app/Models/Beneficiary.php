<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'recipient_name',

        'recipient_account',
        'recipient_bank',

        'recipient_phone',
        'recipient_network',

        // 'biller_code',

        'metadata',

        // 'is_favorite',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
