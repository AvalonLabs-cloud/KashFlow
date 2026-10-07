<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\MustVerifyTransferTransactionEligibility;
use App\Contracts\MustVerifyAccountEligibility;
use App\Contracts\MustVerifyBalanceEligibility;
use App\Concerns\CanVerifyTransferTransactionEligibility;
use App\Concerns\CanVerifyAccountEligibility;
use App\Concerns\CanVerifyBalanceEligibility;
use App\AccountStatus;
use App\Domains\Transactions\Services\RetrieveWalletBalance;

class Account extends Model implements MustVerifyAccountEligibility, MustVerifyBalanceEligibility
{
    use CanVerifyAccountEligibility, CanVerifyBalanceEligibility;

    protected $table = 'accounts';

    protected $fillable = [
        'user_id',
        'order_ref',
        'flw_ref',
        'account_number',
        'bank_name',
        'account_name',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function otps()
    // {
    //     return $this->hasMany(Otp::class);
    // }


    public function previous_phones()
    {
        return $this->hasMany(PreviousPhoneNumber::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function holds()
    {
        return $this->hasMany(Hold::class);
    }

        public function transcactionFee()
    {
        return $this->hasOne(TransactionFeeSetting::class);
    }


    public function ledger_entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function pin()
    {
        return $this->hasOne(Pin::class);
    }

        public function recovery_pins()
    {
        return $this->hasOne(RecoveryPin::class);
    }

    public function walletBalance(){
        return app(RetrieveWalletBalance::class)->calculateAvailableBalance();
    }

    protected function casts(){
        return [
            'status' => AccountStatus::class,
        ];
    }

}
