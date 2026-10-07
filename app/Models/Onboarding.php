<?php

namespace App\Models;

use App\OnboardingPhase;
use Illuminate\Database\Eloquent\Model;

class Onboarding extends Model
{
    protected $fillable = [
        'user_id',
        'phase',
        'is_phone_verified',
        'is_authenticated',
        'is_completed',
    ];

public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'phase' => OnboardingPhase::class,
        ];
    }
}
