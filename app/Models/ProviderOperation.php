<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderOperation extends Model
{
    protected $fillable = [
        'transaction_id',
        'provider',
        'operation',
        'client_reference',
        'provider_reference',
        'status',
        'attempt_number',
        'response_code',
        'failure_reason',
        'request_payload',
        'response_payload',
        'requested_at',
        'last_attempt_at',
        'completed_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'requested_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}