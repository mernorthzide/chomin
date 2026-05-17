@props(['product', 'sizeRef' => 'selectedSize', 'colorRef' => 'selectedColorName'])
@php $isEn = app()->getLocale() === 'en'; @endphp

<div x-data="backInStockForm({
        productSlug: '{{ $product->slug }}',
        endpoint: '{{ route('products.back-in-stock', $product->slug) }}',
        csrf: '{{ csrf_token() }}',
     })" class="mt-4">
    <button type="button" @click="open = !open"
            class="text-xs uppercase tracking-[0.14em] underline-offset-4 underline hover:opacity-60">
        {{ $isEn ? 'Notify me when back in stock' : 'แจ้งเตือนเมื่อสินค้ากลับมา' }}
    </button>

    <div x-show="open" x-cloak x-transition class="mt-3 border border-brand-gray-border p-4">
        <form @submit.prevent="submit({{ $sizeRef }}, {{ $colorRef }})" class="flex flex-col gap-2 sm:flex-row">
            <input type="email" x-model="email" required placeholder="{{ $isEn ? 'your@email.com' : 'อีเมลของคุณ' }}"
                   class="flex-1 border border-brand-gray-border px-3 py-2 text-sm">
            <button type="submit" :disabled="loading"
                    class="bg-brand-black px-4 py-2 text-xs uppercase tracking-[0.14em] text-white disabled:opacity-50">
                <span x-show="!loading">{{ $isEn ? 'Notify me' : 'แจ้งเตือน' }}</span>
                <span x-show="loading">…</span>
            </button>
        </form>
        <template x-if="message">
            <p class="mt-2 text-xs" :class="success ? 'text-green-700' : 'text-red-700'" x-text="message"></p>
        </template>
    </div>
</div>

<script>
function backInStockForm(config) {
    return {
        open: false,
        email: '',
        loading: false,
        success: false,
        message: '',
        async submit(size, color) {
            this.loading = true;
            this.message = '';
            try {
                const res = await fetch(config.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                    },
                    body: JSON.stringify({
                        email: this.email,
                        size: size || null,
                        color: color || null,
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok) {
                    this.success = true;
                    this.message = data.message || '{{ $isEn ? "We will notify you." : "เราจะแจ้งเตือนคุณ" }}';
                    this.email = '';
                } else {
                    this.success = false;
                    this.message = (data.errors?.email?.[0]) || '{{ $isEn ? "Please try again." : "กรุณาลองอีกครั้ง" }}';
                }
            } catch (e) {
                this.success = false;
                this.message = '{{ $isEn ? "Network error" : "เครือข่ายผิดพลาด" }}';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
