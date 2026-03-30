<x-mail::message>
# ออเดอร์ใหม่เข้ามา

**เลขออเดอร์:** {{ $order->order_number }}
**ลูกค้า:** {{ $order->user->name }} ({{ $order->user->email }})
**วันที่:** {{ $order->created_at->format('d/m/Y H:i') }}
**ยอดรวม:** ฿{{ number_format($order->total, 0) }}

## รายการสินค้า

<x-mail::table>
| สินค้า | จำนวน | ราคา |
|:-------|:------:|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ฿{{ number_format($item->price * $item->quantity, 0) }} |
@endforeach
</x-mail::table>

## ที่อยู่จัดส่ง

{{ $order->shipping_name }} | {{ $order->shipping_phone }}
{{ $order->shipping_address }} {{ $order->shipping_district }} {{ $order->shipping_province }} {{ $order->shipping_postal_code }}

<x-mail::button :url="url('/admin/orders')">
จัดการออเดอร์ในระบบ Admin
</x-mail::button>
