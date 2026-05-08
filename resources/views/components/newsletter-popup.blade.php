@if(\App\Models\SiteSetting::get('newsletter_popup_enabled', '0') === '1')
<div
    x-data="{
        open: false,
        init() {
            const last = Number(localStorage.getItem('chomin_newsletter_popup_at') || 0);
            const cooldown = 14 * 24 * 60 * 60 * 1000;
            this.open = Date.now() - last > cooldown;
        },
        close() {
            localStorage.setItem('chomin_newsletter_popup_at', Date.now().toString());
            this.open = false;
        }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[65] flex items-end sm:items-center justify-center bg-brand-black/35 px-4 py-6"
>
    <div class="w-full max-w-md bg-white border border-brand-gray-border p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <p class="text-[11px] uppercase tracking-[0.2em] text-brand-gray-light mb-2">CHOMIN</p>
                <h2 class="font-serif text-3xl uppercase leading-none">{{ app()->getLocale() === 'en' ? 'Stay in color' : 'รับข่าวสีใหม่ก่อนใคร' }}</h2>
            </div>
            <button type="button" @click="close()" class="text-brand-gray-medium hover:text-brand-black" aria-label="Close newsletter popup">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('newsletter.store') }}" class="space-y-3" @submit="close()">
            @csrf
            <input type="hidden" name="source" value="popup">
            <input type="email" name="email" required placeholder="Email" class="w-full border-brand-gray-border text-sm">
            <button type="submit" class="w-full bg-brand-black text-white py-3 text-xs uppercase tracking-[0.15em]">Join</button>
        </form>
    </div>
</div>
@endif
