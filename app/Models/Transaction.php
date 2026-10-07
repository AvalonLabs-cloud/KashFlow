<?php

namespace App\Models;

use App\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_id',
        'reference',
        'type',
        'amount',
        'base_currency',
        'ledger_currency',
        'status',
        'recipient',
        'metadata',
        'description',
        'failure_reason',
        'initiated_at',
        'completed_at',
        'failed_at',
        'direction',
        'transaction_reference',
        'reconsilation_attempt_count',
        'next_reconsilation',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'metadata' => 'array',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function scopeForUser(mixed $query, int $userId)
    {
        return $query->where('user_id', $userId);
    }


    public function scopeCategory(mixed $query, ?string $category)
    {
        return $query->when(
            $category,
            fn($query) => $query->where('type', $category)
        );
    }

    public function scopeDateRange(
        mixed $query,
        ?string $startDate,
        ?string $endDate
    ) {
        return $query
            ->when(
                $startDate,
                fn($query) =>
                $query->whereDate('created_at', '>=', $startDate)
            )
            ->when(
                $endDate,
                fn($query) =>
                $query->whereDate('created_at', '<=', $endDate)
            );
    }

    public function scopeStatuses(mixed $query, ?array $statuses)
    {
        return $query->when(
            !empty($statuses),
            fn($query) => $query->whereIn('status', $statuses)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adminTransaction(): HasOne
    {
        return $this->hasOne(AdminTransaction::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function hold(): HasOne
    {
        return $this->hasOne(Hold::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function providerOperations(): HasMany
    {
        return $this->hasMany(ProviderOperation::class);
    }

    public function transactionReconsilation(): HasOne
    {
        return $this->hasOne(TransactionReconsilation::class);
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', TransactionStatus::PENDING);
    }
}
