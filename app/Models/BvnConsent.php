<?php

namespace App\Models;

use App\BvnConsentStatus;
use Illuminate\Database\Eloquent\Model;

class BvnConsent extends Model
{
    protected $table = 'bvn_consents';

    protected $fillable = [
        'user_id',
        'consent_reference',
        'first_name',
        'last_name',
        'bvn',
        'status',
        'extra'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        protected function casts(): array
    {
        return [
            'status' => BvnConsentStatus::class,
            'extra' => 'array',
        ];
    }
}
