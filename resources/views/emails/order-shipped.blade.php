<x-mail::message>
# สินค้าของคุณถูกจัดส่งแล้ว

สวัสดีคุณ **{{ $order->user->name }}**,

ออเดอร์ **{{ $order->order_number }}** ของคุณได้ถูกส่งออกไปแล้ว

## ข้อมูลการจัดส่ง

**บริษัทขนส่ง:** {{ $order->carrier_name }}
**เลขพัสดุ:** {{ $order->tracking_number }}
**วันที่จัดส่ง:** {{ $order->shipped_at?->format('d/m/Y H:i') }}

<x-mail::button :url="route('orders.show', $order)">
ดูรายละเอียดออเดอร์
</x-mail::button>

ขอบคุณที่ไว้วางใจ **{{ config('app.name') }}**
