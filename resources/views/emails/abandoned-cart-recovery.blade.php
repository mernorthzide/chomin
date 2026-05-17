<x-mail::message>
# ลืมอะไรไว้ในตะกร้าไหม? 🛒

สวัสดี,

คุณมีสินค้าค้างอยู่ในตะกร้า รีบกลับมาก่อนที่จะหมด!

<x-mail::table>
| สินค้า | ขนาด | จำนวน | ราคา |
|--------|------|--------|------|
@foreach($abandonedCart->items_snapshot as $item)
| {{ $item['product_name'] ?? '-' }} | {{ $item['size'] ?? '-' }} | {{ $item['quantity'] ?? 1 }} | ฿{{ number_format($item['price'] ?? 0, 0) }} |
@endforeach
</x-mail::table>

**ยอดรวม:** ฿{{ number_format($abandonedCart->total, 0) }}

<x-mail::button :url="route('shop.index', ['utm_source' => 'email', 'utm_medium' => 'abandoned_cart', 'utm_campaign' => 'recovery'])">
กลับไปดูสินค้าในตะกร้า
</x-mail::button>

ขอบคุณที่สนใจสินค้าของ **{{ config('app.name') }}**

หากมีคำถาม สามารถติดต่อเราได้ที่ตลอดเวลา
</x-mail::message>
