<div
    x-data="{
        open: localStorage.getItem('chomin_cookie_consent') === null,
        save(categories) {
            const payload = { necessary: true, analytics: !!categories.analytics, marketing: !!categories.marketing, embeds: !!categories.embeds };
            localStorage.setItem('chomin_cookie_consent', JSON.stringify(payload));
            window.dispatchEvent(new CustomEvent('chomin:consent-updated', { detail: payload }));
            fetch('{{ route('cookies.consent') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ categories: payload })
            }).catch(() => {});
            this.open = false;
        }
    }"
    x-show="open"
    x-cloak
    class="cookie-consent-panel fixed inset-x-0 bottom-0 z-[70] max-h-[38svh] overflow-y-auto border-t border-brand-gray-border bg-white px-4 py-3 shadow-[0_-12px_36px_rgba(26,26,26,0.10)] md:inset-x-6 md:bottom-6 md:mx-auto md:max-h-none md:max-w-5xl md:border md:px-5 md:py-4 md:shadow-[0_18px_60px_rgba(26,26,26,0.12)]"
>
    <div class="mx-auto flex max-w-5xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs leading-relaxed text-brand-gray-dark sm:text-sm md:max-w-2xl">
            {{ app()->getLocale() === 'en'
                ? 'CHOMIN uses necessary cookies and loads optional analytics, marketing, and media only with your consent.'
                : 'CHOMIN ใช้คุกกี้จำเป็น และเปิดใช้คุกกี้วิเคราะห์ การตลาด หรือสื่อภายนอกเมื่อคุณยินยอมเท่านั้น' }}
        </p>
        <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:gap-3">
            <button type="button" @click="save({ analytics: false, marketing: false, embeds: false })" class="min-h-[44px] border border-brand-black px-3 py-2 text-xs uppercase tracking-[0.12em] sm:px-5">
                {{ app()->getLocale() === 'en' ? 'Reject optional' : 'ปฏิเสธคุกกี้เสริม' }}
            </button>
            <button type="button" @click="save({ analytics: true, marketing: true, embeds: true })" class="min-h-[44px] bg-brand-black px-3 py-2 text-xs uppercase tracking-[0.12em] text-white sm:px-5">
                {{ app()->getLocale() === 'en' ? 'Accept all' : 'ยอมรับทั้งหมด' }}
            </button>
        </div>
    </div>
</div>
