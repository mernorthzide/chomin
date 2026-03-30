<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = ['shipping_fee', 'free_shipping_min_amount'];
    protected $casts = ['shipping_fee' => 'decimal:2', 'free_shipping_min_amount' => 'decimal:2'];

    public static function current(): static
    {
        return static::first() ?? static::create(['shipping_fee' => 50.00]);
    }

    public function getShippingFeeFor(float $subtotal): float
    {
        if ($this->free_shipping_min_amount && $subtotal >= $this->free_shipping_min_amount) return 0;
        return (float) $this->shipping_fee;
    }
}
