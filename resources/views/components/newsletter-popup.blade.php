@php $isEn = app()->getLocale() === 'en'; @endphp

<div x-data="newsletterPopup()" x-init="init()"
     x-show="open" x-cloak
     @keydown.escape.window="close()"
     class="fixed inset-0 z-[85] flex items-end justify-center bg-brand-black/50 px-4 pb-6 md:items-center md:pb-0"
     x-transition.opacity
     @click.self="dismiss()">
    <div class="relative grid w-full max-w-3xl grid-cols-1 overflow-hidden bg-white md:grid-cols-5"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">

        <button @click="dismiss()" aria-label="Close"
                class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center bg-white/80 hover:bg-white">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="md:col-span-2 bg-brand-gray flex items-center justify-center min-h-[200px]">
            <span class="font-serif text-7xl uppercase text-brand-black leading-none">CHO<br>MIN</span>
        </div>

        <div class="md:col-span-3 p-6 md:p-10 flex flex-col justify-center">
            <template x-if="!success">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Join the list' : 'สมัครรับข่าวสาร' }}</p>
                    <h2 class="mt-2 font-serif text-3xl uppercase leading-tight md:text-4xl">
                        {{ $isEn ? '10% off your first order' : 'รับส่วนลด 10% ครั้งแรก' }}
                    </h2>
                    <p class="mt-3 text-sm leading-relaxed text-brand-gray-medium">
                        {{ $isEn
                            ? 'Be the first to hear about new drops, restocks, and members-only offers.'
                            : 'รับข่าวสารคอลเล็คชันใหม่ สินค้าเข้าใหม่ และโปรพิเศษเฉพาะสมาชิก' }}
                    </p>
                    <form @submit.prevent="submit()" class="mt-5 flex flex-col gap-2 sm:flex-row">
                        <input type="email" x-model="email" required
                               placeholder="{{ $isEn ? 'your@email.com' : 'อีเมลของคุณ' }}"
                               class="flex-1 border border-brand-gray-border px-4 py-3 text-sm">
                        <button type="submit" :disabled="loading"
                                class="bg-brand-black px-5 py-3 text-xs uppercase tracking-[0.16em] text-white disabled:opacity-50">
                            <span x-show="!loading">{{ $isEn ? 'Get 10% off' : 'รับส่วนลด' }}</span>
                            <span x-show="loading">…</span>
                        </button>
                    </form>
                    <button @click="dismiss()" class="mt-3 text-[11px] uppercase tracking-[0.14em] text-brand-gray-light underline-offset-4 underline">
                        {{ $isEn ? 'No thanks' : 'ไม่ขอบคุณ' }}
                    </button>
                    <p class="mt-4 text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">
                        {{ $isEn ? 'You can unsubscribe at any time.' : 'ยกเลิกได้ทุกเมื่อ' }}
                    </p>
                </div>
            </template>
            <template x-if="success">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Welcome' : 'ยินดีต้อนรับ' }}</p>
                    <h2 class="mt-2 font-serif text-3xl uppercase leading-tight md:text-4xl">
                        {{ $isEn ? 'Your code is ready' : 'รหัสส่วนลดพร้อมใช้' }}
                    </h2>
                    <div class="mt-5 border-2 border-dashed border-brand-black p-4 text-center">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Use at checkout' : 'ใช้ตอนชำระเงิน' }}</p>
                        <p class="mt-2 font-serif text-3xl uppercase" x-text="couponCode || 'WELCOME10'"></p>
                        <button type="button" @click="copyCode()" class="mt-3 text-[11px] uppercase tracking-[0.14em] underline">
                            <span x-text="copied ? '{{ $isEn ? 'Copied' : 'คัดลอกแล้ว' }}' : '{{ $isEn ? 'Copy code' : 'คัดลอกรหัส' }}'"></span>
                        </button>
                    </div>
                    <a href="{{ route('shop.index') }}" class="mt-5 block w-full bg-brand-black px-5 py-3 text-center text-xs uppercase tracking-[0.16em] text-white">
                        {{ $isEn ? 'Start shopping' : 'ไปช้อปเลย' }}
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function newsletterPopup() {
    return {
        open: false,
        email: '',
        loading: false,
        success: false,
        copied: false,
        couponCode: '',
        init() {
            if (localStorage.getItem('chomin_newsletter_dismissed') || localStorage.getItem('chomin_newsletter_subscribed')) return;

            const delay = {{ (int) config('chomin.newsletter.popup_delay_ms', 25000) }};
            const timer = setTimeout(() => { this.show(); cleanup(); }, delay);
            const onScroll = () => {
                const percent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight;
                if (percent > 0.5) { this.show(); cleanup(); }
            };
            const onExit = (e) => { if (e.clientY < 10) { this.show(); cleanup(); } };
            const cleanup = () => {
                clearTimeout(timer);
                window.removeEventListener('scroll', onScroll);
                document.removeEventListener('mouseleave', onExit);
                window.removeEventListener('pagehide', cleanup);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            document.addEventListener('mouseleave', onExit);
            window.addEventListener('pagehide', cleanup);
        },
        show() {
            if (localStorage.getItem('chomin_newsletter_dismissed') || localStorage.getItem('chomin_newsletter_subscribed')) return;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() { this.open = false; document.body.style.overflow = ''; },
        dismiss() {
            localStorage.setItem('chomin_newsletter_dismissed', Date.now());
            this.close();
        },
        async submit() {
            this.loading = true;
            try {
                const locale = document.documentElement.lang || 'th';
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch(`/${locale}/newsletter`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ email: this.email, source: 'popup', with_coupon: true }),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok) {
                    this.success = true;
                    this.couponCode = data.coupon || 'WELCOME10';
                    localStorage.setItem('chomin_newsletter_subscribed', '1');
                }
            } catch (e) { console.error(e); }
            finally { this.loading = false; }
        },
        copyCode() {
            navigator.clipboard?.writeText(this.couponCode);
            this.copied = true;
            setTimeout(() => this.copied = false, 1500);
        },
    };
}
</script>
