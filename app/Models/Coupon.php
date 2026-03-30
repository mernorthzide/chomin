<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'type', 'value', 'max_discount', 'min_order_amount', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active'];
    protected $casts = ['value' => 'decimal:2', 'max_discount' => 'decimal:2', 'min_order_amount' => 'decimal:2', 'is_active' => 'boolean', 'starts_at' => 'datetime', 'expires_at' => 'datetime'];

    public function isValid(float $orderAmount = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($orderAmount < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'fixed') return min($this->value, $subtotal);
        $discount = $subtotal * ($this->value / 100);
        if ($this->max_discount !== null) $discount = min($discount, $this->max_discount);
        return round($discount, 2);
    }
}
