<x-mail::message>
# กรุณาแนบสลิปใหม่

สวัสดีคุณ **{{ $order->user->name }}**,

เราไม่สามารถยืนยันสลิปการโอนของออเดอร์ **{{ $order->order_number }}** ได้

@if($order->paymentSlip && $order->paymentSlip->rejection_reason)
**เหตุผล:** {{ $order->paymentSlip->rejection_reason }}
@endif

กรุณาตรวจสอบข้อมูลและอัปโหลดสลิปใหม่

<x-mail::button :url="route('orders.show', $order)">
อัปโหลดสลิปใหม่
</x-mail::button>

หากมีข้อสงสัย กรุณาติดต่อทีมงาน **{{ config('app.name') }}**
