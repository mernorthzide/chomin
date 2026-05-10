<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Collections' : 'คอลเล็คชัน').' | CHOMIN'">

    <section class="px-6 md:px-12 py-12 md:py-16 border-b border-brand-gray-border bg-white">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Campaigns</p>
                <h1 class="font-serif uppercase leading-none text-5xl md:text-7xl">
                    {{ app()->getLocale() === 'en' ? 'Collections' : 'คอลเล็คชัน' }}
                </h1>
            </div>
            <p class="max-w-md text-sm text-brand-gray-medium leading-relaxed">
                รวมแคมเปญและคอลเล็คชันจาก CHOMIN สำหรับเลือกเชิ้ต สี และ styling direction ที่ใช่
            </p>
        </div>
    </section>

    <section class="bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 border-t border-brand-gray-border">
            @forelse($collections as $collection)
                @php
                    $collectionImage = $collection->banner_image
                        ? \Illuminate\Support\Facades\Storage::url($collection->banner_image)
                        : ($collection->image ? \Illuminate\Support\Facades\Storage::url($collection->image) : null);
                @endphp
                <a href="{{ route('collections.show', $collection->slug) }}"
                   class="collection-card group border-b border-r border-brand-gray-border bg-white focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-inset">
                    <div class="aspect-[4/5] bg-brand-gray overflow-hidden">
                        @if($collectionImage)
                            <img src="{{ $collectionImage }}"
                                 alt="{{ $collection->localized_name }}"
                                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                                 loading="lazy">
                        @else
                            <div class="h-full w-full flex items-center justify-center">
                                <span class="font-serif text-6xl text-brand-gray-border">{{ strtoupper(substr($collection->localized_name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="min-h-[132px] px-4 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="text-xs uppercase tracking-[0.08em] leading-snug">{{ $collection->localized_name }}</h2>
                            <span class="collection-count text-[10px] uppercase tracking-[0.08em] text-brand-gray-light whitespace-nowrap">
                                {{ $collection->products_count }} items
                            </span>
                        </div>
                        @if($collection->localized_description)
                            <p class="mt-2 text-xs text-brand-gray-medium leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($collection->localized_description, 80) }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-full px-6 py-24 text-center">
                    <p class="text-sm text-brand-gray-medium">ยังไม่มีคอลเล็คชัน</p>
                </div>
            @endforelse
        </div>
    </section>

</x-layouts.shop>
