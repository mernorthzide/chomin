<x-mail::message>
# มีสลิปรอตรวจสอบ

**เลขออเดอร์:** {{ $order->order_number }}
**ลูกค้า:** {{ $order->user->name }} ({{ $order->user->email }})
**ยอดรวม:** ฿{{ number_format($order->total, 0) }}
**อัปโหลดสลิปเมื่อ:** {{ $order->paymentSlip?->uploaded_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

<x-mail::button :url="url('/admin/orders')">
ตรวจสอบสลิปในระบบ Admin
</x-mail::button>
