<x-layouts.shop :title="$title" :description="$description">
    <article class="px-6 md:px-12 py-16 md:py-24">
        <div class="max-w-3xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase leading-none mb-8">{{ $story->localized('title') }}</h1>
            @if($story->localized('excerpt'))
                <p class="text-lg text-brand-gray-dark mb-10">{{ $story->localized('excerpt') }}</p>
            @endif
            <div class="whitespace-pre-line leading-relaxed text-brand-gray-dark">{{ $story->localized('body') }}</div>
        </div>
    </article>
</x-layouts.shop>
