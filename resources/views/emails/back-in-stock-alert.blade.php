<x-mail::message>
# {{ $notification->product->name ?? 'สินค้า' }} กลับมาแล้ว!

สวัสดี,

สินค้าที่คุณฝากแจ้งเตือนไว้มีสินค้าพร้อมจำหน่ายแล้ว รีบสั่งก่อนหมดนะ!

---

**{{ $notification->product->name ?? 'สินค้า' }}**

@if($notification->size)
**ขนาด:** {{ $notification->size }}
@endif

@if($notification->color)
**สี:** {{ $notification->color }}
@endif

@if($notification->product)
**ราคา:** ฿{{ number_format($notification->product->display_price, 0) }}
@endif

@if($notification->product && $notification->product->primaryImage)
![{{ $notification->product->name }}]({{ $notification->product->primaryImage->url }})
@endif

<x-mail::button :url="route('shop.product', ['slug' => $notification->product->slug ?? ''])">
ซื้อเลยก่อนหมด
</x-mail::button>

ขอบคุณที่ไว้วางใจ **{{ config('app.name') }}**
</x-mail::message>
