<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'code_hash', 'code_last4', 'initial_balance', 'balance', 'currency', 'status',
        'recipient_email', 'recipient_name', 'message', 'issued_by', 'expires_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'balance' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public static function hashCode(string $code): string
    {
        return hash('sha256', trim($code));
    }

    public static function generatePlainCode(): string
    {
        return 'CHOMIN-'.Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4));
    }

    public static function findRedeemable(string $code): ?self
    {
        return static::where('code_hash', static::hashCode($code))->first();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isRedeemable(): bool
    {
        return $this->status === 'active'
            && (float) $this->balance > 0
            && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
