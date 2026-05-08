<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Color Library' : 'คลังสี').' | CHOMIN'">
    <section class="px-6 md:px-12 py-12 md:py-16 border-b border-brand-gray-border bg-white">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Color Library</p>
                <h1 class="font-serif uppercase leading-none text-5xl md:text-7xl">
                    {{ app()->getLocale() === 'en' ? 'Color Library' : 'คลังสี' }}
                </h1>
            </div>
            <p class="max-w-md text-sm text-brand-gray-medium leading-relaxed">
                เลือกจากเฉดผ้าจริงของ CHOMIN แล้วเข้า shop ด้วยสีที่เลือกทันที
            </p>
        </div>
    </section>

    <section class="bg-white">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 border-t border-brand-gray-border">
            @foreach($colors as $color)
                @php
                    $imageColorCode = pathinfo($color->images->first()?->image_path ?? '', PATHINFO_FILENAME);
                    $colorKey = $color->slug ?: ($imageColorCode ?: \Illuminate\Support\Str::slug($color->name));
                @endphp
                <a href="{{ route('shop.index', ['color' => $colorKey]) }}"
                   class="group border-b border-r border-brand-gray-border bg-white p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-inset">
                    <span class="block aspect-square border border-brand-gray-border transition-transform duration-500 group-hover:scale-[0.98]"
                          style="background: {{ $color->color_code }}"></span>
                    <span class="mt-4 block text-xs uppercase tracking-[0.08em] leading-snug">{{ $color->localized_name }}</span>
                    <span class="mt-1 block text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">Shop this color</span>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.shop>
