<x-mail::message>
# ออเดอร์สำเร็จ ขอบคุณ!

สวัสดีคุณ **{{ $order->user->name }}**,

ออเดอร์ **{{ $order->order_number }}** สำเร็จแล้ว ขอบคุณมากที่ซื้อสินค้ากับเรา

@if($order->points_earned > 0)
**แต้มที่ได้รับ:** +{{ number_format($order->points_earned) }} แต้ม (สามารถใช้เป็นส่วนลดในการสั่งซื้อครั้งถัดไป)
@endif

<x-mail::button :url="route('shop.index')">
เลือกซื้อสินค้าต่อ
</x-mail::button>

ขอบคุณที่ไว้วางใจ **{{ config('app.name') }}**
