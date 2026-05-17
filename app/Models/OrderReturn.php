<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'rma_number', 'order_id', 'user_id', 'type', 'reason', 'reason_detail',
        'items', 'status', 'refund_amount', 'photos', 'admin_note',
        'approved_at', 'refunded_at',
    ];

    protected $casts = [
        'items' => 'array',
        'photos' => 'array',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateRmaNumber(): string
    {
        $date = now()->format('Ymd');
        $last = static::where('rma_number', 'like', "RMA-{$date}-%")->orderByDesc('rma_number')->first();
        $sequence = $last ? intval(substr($last->rma_number, -4)) + 1 : 1;

        return sprintf('RMA-%s-%04d', $date, $sequence);
    }

    public function getStatusLabelAttribute(): string
    {
        $isEn = app()->getLocale() === 'en';

        return match ($this->status) {
            'requested' => $isEn ? 'Requested' : 'รอตรวจสอบ',
            'approved' => $isEn ? 'Approved' : 'อนุมัติ',
            'in_transit' => $isEn ? 'In transit' : 'กำลังจัดส่งคืน',
            'received' => $isEn ? 'Received' : 'รับสินค้าคืนแล้ว',
            'refunded' => $isEn ? 'Refunded' : 'คืนเงินสำเร็จ',
            'rejected' => $isEn ? 'Rejected' : 'ไม่อนุมัติ',
            'cancelled' => $isEn ? 'Cancelled' : 'ยกเลิก',
            default => $this->status,
        };
    }

    public function getReasonLabelAttribute(): string
    {
        $isEn = app()->getLocale() === 'en';

        return match ($this->reason) {
            'size_too_small' => $isEn ? 'Size too small' : 'ไซส์เล็กไป',
            'size_too_large' => $isEn ? 'Size too large' : 'ไซส์ใหญ่ไป',
            'color_different' => $isEn ? 'Color looks different' : 'สีไม่ตรงตามภาพ',
            'defective' => $isEn ? 'Defective / damaged' : 'สินค้าชำรุด',
            'not_as_described' => $isEn ? 'Not as described' : 'ไม่ตรงกับรายละเอียด',
            'changed_mind' => $isEn ? 'Changed mind' : 'เปลี่ยนใจ',
            default => $isEn ? 'Other' : 'อื่น ๆ',
        };
    }
}
