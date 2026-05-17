@if(session('success') || session('error') || session('warning'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 left-1/2 z-[200] -translate-x-1/2"
        role="status"
        aria-live="polite">
        <div class="flex items-center gap-3 border px-5 py-3 text-xs uppercase tracking-[0.12em] shadow-lg
            @if(session('error')) border-red-800 bg-red-800 text-white
            @elseif(session('warning')) border-amber-700 bg-amber-700 text-white
            @else border-brand-black bg-brand-black text-white @endif">
            <span>{{ session('success') ?? session('error') ?? session('warning') }}</span>
            <button @click="show = false" class="opacity-60 hover:opacity-100 focus:outline-none" aria-label="ปิด">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
