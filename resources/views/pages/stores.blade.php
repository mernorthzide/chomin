<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Stores' : 'ช่องทางติดต่อ').' | CHOMIN'">
    <section class="px-6 md:px-12 py-16">
        <div class="max-w-5xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-12">{{ app()->getLocale() === 'en' ? 'Stores' : 'ช่องทางติดต่อ' }}</h1>
            <div class="space-y-8">
                @foreach($locations as $location)
                    <article class="border-t border-brand-gray-border pt-8">
                        <h2 class="text-xl">{{ $location->localized('name') }}</h2>
                        <p class="mt-3 text-brand-gray-dark whitespace-pre-line">{{ $location->localized('address') }}</p>
                        <p class="mt-2 text-sm text-brand-gray-light whitespace-pre-line">{{ $location->localized('hours') }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.shop>
