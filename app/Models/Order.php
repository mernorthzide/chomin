<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'shipping_fee', 'discount', 'total',
        'points_earned', 'points_used', 'coupon_id',
        'shipping_name', 'shipping_phone', 'shipping_address', 'shipping_district',
        'shipping_province', 'shipping_postal_code',
        'tracking_number', 'carrier_name', 'shipped_at', 'completed_at', 'cancelled_at', 'note',
    ];
    protected $casts = [
        'subtotal' => 'decimal:2', 'shipping_fee' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2',
        'shipped_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function paymentSlip(): HasOne { return $this->hasOne(PaymentSlip::class); }
    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::where('order_number', 'like', "CHO-{$date}-%")->orderByDesc('order_number')->first();
        $sequence = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
        return sprintf('CHO-%s-%04d', $date, $sequence);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'รอชำระเงิน', 'awaiting_payment' => 'รอตรวจสอบ', 'paid' => 'ชำระเงินแล้ว',
            'shipping' => 'กำลังจัดส่ง', 'completed' => 'สำเร็จ', 'cancelled' => 'ยกเลิก', default => $this->status,
        };
    }
}
