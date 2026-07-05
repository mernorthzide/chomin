<x-layouts.shop :title="$title">
    <section class="px-6 md:px-12 py-16 md:py-24">
        <div class="max-w-4xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-12">
                {{ app()->getLocale() === 'en' ? 'FAQ' : 'คำถามที่พบบ่อย' }}
            </h1>
            <div class="divide-y divide-brand-gray-border">
                @foreach($items as $category => $group)
                    <div class="py-8">
                        <h2 class="text-xs tracking-[0.2em] uppercase text-brand-gray-light mb-6">{{ $category }}</h2>
                        <div class="space-y-2">
                            @foreach($group as $item)
                                <details class="group border-b border-brand-gray-border pb-2">
                                    <summary class="flex cursor-pointer items-center justify-between gap-4 py-2 text-lg font-medium list-none [&::-webkit-details-marker]:hidden">
                                        <span>{{ $item->localized('question') }}</span>
                                        <svg class="h-4 w-4 shrink-0 transition-transform group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </summary>
                                    <p class="mt-2 text-brand-gray-dark leading-relaxed">{{ $item->localized('answer') }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.shop>
