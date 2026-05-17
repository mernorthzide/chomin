@php
    $lineId = \App\Models\SiteSetting::get('line_oa_id', '@chomin.th');
    $lineUrl = \App\Models\SiteSetting::get('line_oa_url', 'https://line.me/R/ti/p/@chomin.th');
    $phone = \App\Models\SiteSetting::get('site_phone');
    $isEn = app()->getLocale() === 'en';
@endphp

<div x-data="{ open: false }" class="fixed bottom-4 right-4 z-[70] flex flex-col items-end gap-2">

    {{-- Expandable contact panel --}}
    <div x-show="open" x-cloak x-transition
         @click.outside="open = false"
         class="w-72 overflow-hidden border border-brand-gray-border bg-white shadow-xl">
        <div class="bg-brand-black p-4 text-white">
            <p class="text-[11px] uppercase tracking-[0.18em] opacity-70">{{ $isEn ? 'Need help?' : 'ต้องการความช่วยเหลือ?' }}</p>
            <p class="mt-1 font-serif text-lg uppercase">{{ $isEn ? 'Talk to CHOMIN' : 'คุยกับ CHOMIN' }}</p>
        </div>
        <div class="divide-y divide-brand-gray-border">
            <a href="{{ $lineUrl }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-4 hover:bg-brand-gray transition-colors">
                <span class="flex h-10 w-10 items-center justify-center bg-[#06C755] text-white font-bold text-sm">L</span>
                <div>
                    <p class="text-xs uppercase tracking-[0.12em] text-brand-gray-medium">LINE Official</p>
                    <p class="text-sm font-medium">{{ $lineId }}</p>
                </div>
            </a>
            @if($phone)
                <a href="tel:{{ $phone }}" class="flex items-center gap-3 p-4 hover:bg-brand-gray transition-colors">
                    <span class="flex h-10 w-10 items-center justify-center border border-brand-black">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.12em] text-brand-gray-medium">{{ $isEn ? 'Phone' : 'โทร' }}</p>
                        <p class="text-sm font-medium">{{ $phone }}</p>
                    </div>
                </a>
            @endif
            <a href="{{ route('faq') }}" class="flex items-center gap-3 p-4 hover:bg-brand-gray transition-colors">
                <span class="flex h-10 w-10 items-center justify-center border border-brand-black text-lg">?</span>
                <div>
                    <p class="text-xs uppercase tracking-[0.12em] text-brand-gray-medium">FAQ</p>
                    <p class="text-sm font-medium">{{ $isEn ? 'Find answers' : 'คำถามที่พบบ่อย' }}</p>
                </div>
            </a>
        </div>
        <div class="bg-brand-gray p-3 text-center text-[10px] uppercase tracking-[0.12em] text-brand-gray-medium">
            {{ $isEn ? 'Reply within 30 min · 9am–9pm' : 'ตอบภายใน 30 นาที · 09:00–21:00' }}
        </div>
    </div>

    {{-- Toggle button --}}
    <button type="button" @click="open = !open" aria-label="Open chat"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-[#06C755] text-white shadow-lg transition-transform hover:scale-105 active:scale-95">
        <svg x-show="!open" class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .629.285.629.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
        </svg>
        <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
