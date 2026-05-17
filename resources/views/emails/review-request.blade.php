<x-mail::message>
# แบ่งปันประสบการณ์ของคุณ — รับ 50 คะแนน

สวัสดีคุณ **{{ $order->user->name ?? '' }}**,

ขอบคุณที่สั่งซื้อสินค้ากับ **{{ config('app.name') }}** เราหวังว่าคุณจะถูกใจสินค้าของเรา

รีวิวสินค้าของคุณวันนี้เพื่อรับ **50 คะแนน** ที่สามารถนำไปใช้เป็นส่วนลดในการสั่งซื้อครั้งถัดไป!

---

**ออเดอร์ #{{ $order->order_number }}**

@foreach($order->items as $item)
<x-mail::panel>
**{{ $item->product_name ?? $item->variant?->product?->name ?? 'สินค้า' }}**
@if($item->size ?? ($item->options['size'] ?? null))
ขนาด: {{ $item->size ?? $item->options['size'] }}
@endif
จำนวน: {{ $item->quantity }} ชิ้น

<x-mail::button :url="route('account.orders.index')">
เขียนรีวิว
</x-mail::button>
</x-mail::panel>
@endforeach

---

*คะแนนจะถูกเพิ่มเข้าบัญชีของคุณภายใน 24 ชั่วโมงหลังจากที่รีวิวได้รับการอนุมัติ*

ขอบคุณที่ไว้วางใจ **{{ config('app.name') }}**
</x-mail::message>
