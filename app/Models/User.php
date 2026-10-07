<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Filament\Models\Contracts\HasName;
use Illuminate\Notifications\Notification;

class User extends Authenticatable implements MustVerifyEmail, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'email_verified_at',
        'password',
        'phone',
        'phone_verified_at',
        'full_name',
        'first_name',
        'middle_name',
        'last_name',
        'tx_ref',
        'bvn',
        'account_creation_idempotency_key',
        'account_number',
        'bank_name',
        'is_admin',
    ];

    public function account()
    {
        return $this->hasOne(Account::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function heldBalances()
    {
        return $this->hasMany(Hold::class);
    }

    public function ledgers()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function onboarding()
    {
        return $this->hasOne(Onboarding::class);
    }

    public function phone()
    {
        return $this->hasOne(PhoneNumber::class);
    }

    public function bvnConsent()
    {
        return $this->hasOne(bvnConsent::class);
    }

    public function history()
    {
        return $this->hasMany(TransactionHistory::class);
    }


    public function getFilamentName(): string
    {
        return $this->full_name
            ?? trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
            ])))
            ?: $this->email
            ?: 'User';
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $parts = preg_split('/\s+/', trim($value));

                return [
                    'full_name' => $value,
                    'first_name' => $parts[0] ?? null,
                    'middle_name' => count($parts) > 2
                        ? implode(' ', array_slice($parts, 1, -1))
                        : null,
                    'last_name' => count($parts) > 1
                        ? end($parts)
                        : null,
                ];
            }
        );
    }

    public function routeNotificationForMail(Notification $notification)
    {
        return $this->email;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'tx_ref',
        'account_creation_idempotency_key',
        'phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            // 'pin' => 'hashed',
        ];
    }
}
