<x-mail::message>
@if($locale === 'en')
# Welcome to CHOMIN

Thank you for subscribing to our newsletter! You'll be the first to know about new arrivals, exclusive offers, and style inspiration.

@if($coupon)
## Your Welcome Gift

As a thank-you for joining us, here's an exclusive discount code just for you:

<x-mail::panel>
**{{ $coupon->code }}**

{{ (int) $coupon->value }}% off your first order
@if($coupon->expires_at)
Valid until: {{ $coupon->expires_at->format('d M Y') }}
@endif
@if($coupon->max_discount)
Maximum discount: ฿{{ number_format($coupon->max_discount, 0) }}
@endif
</x-mail::panel>

*Discount applies to full-price items. Single use only. Cannot be combined with other promotions.*
@endif

<x-mail::button :url="route('shop.index')">
Shop Now
</x-mail::button>

Thank you for joining the **{{ config('app.name') }}** family.

@else
# ยินดีต้อนรับสู่ CHOMIN

ขอบคุณที่สมัครรับข่าวสารจากเรา! คุณจะได้รับข่าวสารสินค้าใหม่ โปรโมชั่นพิเศษ และแรงบันดาลใจด้านสไตล์ก่อนใคร

@if($coupon)
## ของขวัญต้อนรับ

ขอบคุณที่เข้าร่วมกับเรา นี่คือโค้ดส่วนลดพิเศษสำหรับคุณโดยเฉพาะ:

<x-mail::panel>
**{{ $coupon->code }}**

ลด {{ (int) $coupon->value }}% สำหรับการสั่งซื้อครั้งแรก
@if($coupon->expires_at)
ใช้ได้ถึง: {{ $coupon->expires_at->format('d/m/Y') }}
@endif
@if($coupon->max_discount)
ส่วนลดสูงสุด: ฿{{ number_format($coupon->max_discount, 0) }}
@endif
</x-mail::panel>

*ส่วนลดใช้ได้กับสินค้าราคาปกติ ใช้ได้ครั้งเดียว ไม่สามารถใช้ร่วมกับโปรโมชั่นอื่นได้*
@endif

<x-mail::button :url="route('shop.index')">
เลือกซื้อสินค้า
</x-mail::button>

ขอบคุณที่เป็นส่วนหนึ่งของครอบครัว **{{ config('app.name') }}**
@endif
</x-mail::message>
