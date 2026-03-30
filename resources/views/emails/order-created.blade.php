<x-mail::message>
# ขอบคุณสำหรับการสั่งซื้อ

สวัสดีคุณ **{{ $order->user->name }}**,

เราได้รับออเดอร์ของคุณเรียบร้อยแล้ว กรุณาชำระเงินเพื่อดำเนินการต่อ

## รายละเอียดออเดอร์

**เลขออเดอร์:** {{ $order->order_number }}
**วันที่สั่ง:** {{ $order->created_at->format('d/m/Y H:i') }}

<x-mail::table>
| สินค้า | จำนวน | ราคา |
|:-------|:------:|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} {{ $item->variant_label ? "({$item->variant_label})" : '' }} | {{ $item->quantity }} | ฿{{ number_format($item->price * $item->quantity, 0) }} |
@endforeach
| **ยอดรวมสินค้า** | | ฿{{ number_format($order->subtotal, 0) }} |
| **ค่าจัดส่ง** | | ฿{{ number_format($order->shipping_fee, 0) }} |
@if($order->discount > 0)
| **ส่วนลด** | | -฿{{ number_format($order->discount, 0) }} |
@endif
| **ยอดรวมทั้งหมด** | | **฿{{ number_format($order->total, 0) }}** |
</x-mail::table>

## ข้อมูลการชำระเงิน (PromptPay)

กรุณาโอนเงินผ่าน PromptPay และแนบสลิปที่หน้าออเดอร์

<x-mail::button :url="route('orders.show', $order)">
ดูออเดอร์และอัปโหลดสลิป
</x-mail::button>

ขอบคุณที่ไว้วางใจ **{{ config('app.name') }}**
