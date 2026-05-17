<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = [
        'user_id', 'cart_id', 'email', 'session_id',
        'items_snapshot', 'total',
        'reminder_count', 'last_reminder_at', 'recovered_at',
    ];

    protected $casts = [
        'items_snapshot' => 'array',
        'total' => 'decimal:2',
        'last_reminder_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function scopePending($query)
    {
        return $query->whereNull('recovered_at');
    }
}
