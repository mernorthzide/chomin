@props(['subtotal' => 0])
@php $isEn = app()->getLocale() === 'en'; @endphp

<div x-data="shippingCalculator({ subtotal: {{ (float) $subtotal }} })" class="border border-brand-gray-border p-4">
    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Estimate shipping' : 'คำนวณค่าจัดส่ง' }}</p>
    <div class="mt-3 flex gap-2">
        <input type="text" inputmode="numeric" maxlength="5" pattern="\d{5}" x-model="postalCode"
               @input="lookup()"
               placeholder="{{ $isEn ? 'Postal code' : 'รหัสไปรษณีย์' }}"
               class="flex-1 border border-brand-gray-border px-3 py-2 text-sm">
    </div>
    <template x-if="result">
        <div class="mt-3 space-y-1 text-xs">
            <p class="flex justify-between">
                <span class="text-brand-gray-medium">{{ $isEn ? 'Province' : 'จังหวัด' }}</span>
                <span class="font-medium" x-text="result.province || '—'"></span>
            </p>
            <p class="flex justify-between">
                <span class="text-brand-gray-medium">{{ $isEn ? 'Shipping' : 'ค่าจัดส่ง' }}</span>
                <span class="font-medium">
                    <template x-if="result.shipping_fee > 0">
                        <span>฿<span x-text="result.shipping_fee.toLocaleString()"></span></span>
                    </template>
                    <template x-if="result.shipping_fee === 0">
                        <span class="text-green-700 uppercase">{{ $isEn ? 'Free' : 'จัดส่งฟรี' }}</span>
                    </template>
                </span>
            </p>
            <p class="flex justify-between">
                <span class="text-brand-gray-medium">{{ $isEn ? 'Delivery' : 'จัดส่งภายใน' }}</span>
                <span class="font-medium">
                    <span x-text="result.estimated_delivery_days.min"></span>–<span x-text="result.estimated_delivery_days.max"></span>
                    {{ $isEn ? 'days' : 'วัน' }}
                </span>
            </p>
            <template x-if="result.amount_to_free_shipping > 0">
                <p class="mt-2 border-t border-brand-gray-border pt-2 text-[11px] text-brand-gray-medium">
                    {{ $isEn ? 'Spend' : 'สั่งซื้ออีก' }}
                    ฿<span x-text="result.amount_to_free_shipping.toLocaleString()"></span>
                    {{ $isEn ? 'more for free shipping' : 'เพื่อรับจัดส่งฟรี' }}
                </p>
            </template>
        </div>
    </template>
    <template x-if="error">
        <p class="mt-3 text-[11px] text-red-700" x-text="error"></p>
    </template>
</div>

<script>
function shippingCalculator(config) {
    return {
        subtotal: config.subtotal,
        postalCode: '',
        result: null,
        error: '',
        timer: null,
        lookup() {
            clearTimeout(this.timer);
            this.error = '';
            if (!/^\d{5}$/.test(this.postalCode)) {
                this.result = null;
                return;
            }
            this.timer = setTimeout(async () => {
                try {
                    const locale = document.documentElement.lang || 'th';
                    const url = `/${locale}/shipping/lookup?postal_code=${this.postalCode}&subtotal=${this.subtotal}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!data.ok) {
                        this.error = '{{ $isEn ? "Postal code not recognized" : "ไม่พบรหัสไปรษณีย์นี้" }}';
                        this.result = null;
                        return;
                    }
                    this.result = data;
                    // dispatch event for parent forms to auto-fill province
                    window.dispatchEvent(new CustomEvent('chomin:postal-resolved', { detail: data }));
                } catch (e) {
                    this.error = '{{ $isEn ? "Lookup failed" : "ค้นหาผิดพลาด" }}';
                }
            }, 300);
        }
    };
}
</script>
