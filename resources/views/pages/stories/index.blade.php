<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Stories' : 'บทความ').' | CHOMIN'">
    <section class="px-6 md:px-12 py-16">
        <div class="max-w-6xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-12">{{ app()->getLocale() === 'en' ? 'Stories' : 'บทความ' }}</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($stories as $story)
                    <a href="{{ route('stories.show', $story->slug) }}" class="block">
                        <div class="aspect-[4/5] bg-brand-gray mb-5 overflow-hidden">
                            @if($story->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($story->cover_image) }}" alt="{{ $story->localized('title') }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                            @endif
                        </div>
                        <h2 class="text-lg">{{ $story->localized('title') }}</h2>
                        <p class="mt-2 text-sm text-brand-gray-dark">{{ $story->localized('excerpt') }}</p>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $stories->links() }}</div>
        </div>
    </section>
</x-layouts.shop>
