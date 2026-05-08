<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Color Library' : 'คลังสี').' | CHOMIN'">
    <section class="px-6 md:px-12 py-16">
        <div class="max-w-6xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-12">{{ app()->getLocale() === 'en' ? 'Color Library' : 'คลังสี' }}</h1>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach($colors as $color)
                    <a href="{{ route('shop.index', ['color' => $color->slug ?: \Illuminate\Support\Str::slug($color->name)]) }}" class="block border border-brand-gray-border p-4">
                        <span class="block aspect-square border border-brand-gray-border mb-4" style="background: {{ $color->color_code }}"></span>
                        <span class="text-sm">{{ $color->localized_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.shop>
