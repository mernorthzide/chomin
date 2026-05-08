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
                        <div class="space-y-8">
                            @foreach($group as $item)
                                <article>
                                    <h3 class="text-lg font-medium">{{ $item->localized('question') }}</h3>
                                    <p class="mt-3 text-brand-gray-dark leading-relaxed">{{ $item->localized('answer') }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.shop>
