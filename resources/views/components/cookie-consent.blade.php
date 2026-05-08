<div
    x-data="{
        open: localStorage.getItem('chomin_cookie_consent') === null,
        save(categories) {
            const payload = { necessary: true, analytics: !!categories.analytics, marketing: !!categories.marketing, embeds: !!categories.embeds };
            localStorage.setItem('chomin_cookie_consent', JSON.stringify(payload));
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
    class="fixed bottom-0 left-0 right-0 z-[70] bg-white border-t border-brand-gray-border px-6 py-5"
>
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <p class="text-sm text-brand-gray-dark leading-relaxed max-w-2xl">
            {{ app()->getLocale() === 'en'
                ? 'CHOMIN uses necessary cookies for the shopping experience. Optional analytics, marketing, and embedded media cookies load only with your consent.'
                : 'CHOMIN ใช้คุกกี้ที่จำเป็นสำหรับประสบการณ์ช้อปปิ้ง และจะเปิดใช้คุกกี้วิเคราะห์ การตลาด หรือ embed ภายนอกเมื่อได้รับความยินยอมเท่านั้น' }}
        </p>
        <div class="flex flex-wrap gap-3">
            <button type="button" @click="save({ analytics: false, marketing: false, embeds: false })" class="border border-brand-black px-5 py-2 text-xs uppercase tracking-[0.12em]">
                {{ app()->getLocale() === 'en' ? 'Reject optional' : 'ปฏิเสธคุกกี้เสริม' }}
            </button>
            <button type="button" @click="save({ analytics: true, marketing: true, embeds: true })" class="bg-brand-black text-white px-5 py-2 text-xs uppercase tracking-[0.12em]">
                {{ app()->getLocale() === 'en' ? 'Accept all' : 'ยอมรับทั้งหมด' }}
            </button>
        </div>
    </div>
</div>
