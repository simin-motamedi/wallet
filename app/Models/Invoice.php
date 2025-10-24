<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['paid_at', 'state'];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id',
        'wallet_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'expiration_time' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($invoice) {
            $invoice->uuid = (string)Str::uuid();
        });
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return MorphOne
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return Carbon::now() > $this->expiration_time;
    }

    /**
     * @return HasOneThrough
     */
    public function wallet(): HasOneThrough
    {
        return $this->hasOneThrough(
            Wallet::class,
            Transaction::class,
            'transactionable_id',
            'id',
            'id',
            'wallet_id'
        )->where('transactionable_type', self::class);
    }
}
