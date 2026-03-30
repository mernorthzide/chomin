<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'address', 'district', 'province', 'postal_code', 'is_default'];
    protected $casts = ['is_default' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function getFullAddressAttribute(): string { return "{$this->address} {$this->district} {$this->province} {$this->postal_code}"; }
}
