<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFeeSetting extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    public static function calculateFee(float $amount): float
    {
        $percentage = self::where('is_active', true)->value('percentage');

        if ($percentage === null) {
            return 0;
        }

        return round($amount * ((float) $percentage / 100), 2);
    }
}
