<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebHookEvent  extends Model
{
    protected $fillable = [
        'event',
        'event_type',
        'event_id',
        'transaction_reference',
        'provider_reference',
        'amount',
        'currency',
        'status',

        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',

        'payment_type',
        'service_type',

        // 'processing_status',
        // 'processing_attempts',
        'processed_at',
        'processing_error',

        'payload',
        'metadata',

        'provider_created_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',

            'payload' => 'array',
            'metadata' => 'array',

            'provider_created_at' => 'datetime',
            'processed_at' => 'datetime',

            // 'processing_attempts' => 'integer',
        ];
    }
}
