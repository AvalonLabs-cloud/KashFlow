<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    public function adminTransactions(): HasMany
    {
        return $this->HasMany(AdminTransaction::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (static::exists()) {
                throw new \Exception('Only one record is allowed for this model.');
            }
        });
    }
}
