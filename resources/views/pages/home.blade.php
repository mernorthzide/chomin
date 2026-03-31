<x-layouts.shop>

    @forelse($collections as $index => $collection)
        @php
            // Use admin-selected layout, or auto-cycle by index
            $layoutMap = [
                'side-hero' => 0,
                'dark-editorial' => 1,
                'centered-banner' => 2,
                'header-banner' => 3,
            ];
            $layoutVariant = $collection->layout_type
                ? ($layoutMap[$collection->layout_type] ?? ($index % 4))
                : ($index % 4);

            // Resolve collection hero image (admin-uploaded only)
            $collectionImage = null;
            if ($collection->banner_image) {
                $collectionImage = \Illuminate\Support\Facades\Storage::url($collection->banner_image);
            } elseif ($collection->image) {
                $collectionImage = \Illuminate\Support\Facades\Storage::url($collection->image);
            }

            $collectionNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        @endphp

        {{-- ============================================================
             LAYOUT VARIANT 0: Side-by-side Hero (75% image + 25% panel)
        ============================================================ --}}
        @if($layoutVariant === 0)
            <section class="mb-16 md:mb-24">
                <!-- Hero -->
                <div class="relative w-full min-h-[50vh] md:h-[60vh] flex flex-col md:flex-row items-stretch overflow-hidden mb-12">
                    <div class="w-full md:w-3/4 relative bg-brand-gray">
                        @if($collectionImage)
                            <img src="{{ $collectionImage }}"
                                 alt="{{ $collection->name }}"
                                 class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="w-full md:w-1/4 bg-brand-gray flex flex-col justify-center px-8 md:px-12 py-12">
                        <span class="text-[10px] tracking-[0.5em] uppercase mb-4 text-brand-gray-light">Collection {{ $collectionNumber }}</span>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-normal leading-none mb-6 editorial-title uppercase">
                            {{ $collection->name }}
                        </h2>
                        @if($collection->description)
                            <p class="text-[11px] text-brand-gray-medium mb-10 leading-relaxed uppercase tracking-widest">
                                {{ $collection->description }}
                            </p>
                        @endif
                        <a href="{{ route('collections.show', $collection->slug) }}"
                           class="w-full bg-brand-black text-white py-4 text-center text-[10px] font-bold tracking-[0.3em] uppercase hover:bg-brand-gray-dark transition-colors duration-300 inline-block">
                            ช้อปคอลเลกชัน
                        </a>
                    </div>
                </div>

                <!-- Product Grid -->
                @if($collection->products->isNotEmpty())
                    <div class="px-6 md:px-12">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-12">
                            @foreach($collection->products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

        {{-- ============================================================
             LAYOUT VARIANT 1: Dark Background + Large Italic Title
        ============================================================ --}}
        @elseif($layoutVariant === 1)
            <section class="bg-brand-black text-white py-16 md:py-24 mb-16 md:mb-24">
                <!-- Editorial Hero -->
                <div class="max-w-[1400px] mx-auto px-6 md:px-12 mb-16 md:mb-20">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                        <div>
                            <span class="text-[10px] tracking-[0.5em] uppercase mb-6 text-brand-gray-light block">Collection {{ $collectionNumber }}</span>
                            <h2 class="text-5xl md:text-7xl lg:text-8xl font-serif italic font-normal mb-8 leading-tight tracking-tighter uppercase">
                                {{ $collection->name }}
                            </h2>
                            @if($collection->description)
                                <p class="text-brand-gray-light text-sm mb-12 max-w-sm leading-relaxed uppercase tracking-wider">
                                    {{ $collection->description }}
                                </p>
                            @endif
                            <a href="{{ route('collections.show', $collection->slug) }}"
                               class="text-[11px] font-bold tracking-[0.4em] uppercase border-b-2 border-white pb-2 hover:opacity-50 transition-opacity duration-300">
                                สำรวจคอลเลกชัน
                            </a>
                        </div>
                        <div class="aspect-square overflow-hidden bg-brand-gray-dark">
                            @if($collectionImage)
                                <img src="{{ $collectionImage }}"
                                     alt="{{ $collection->name }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Grid (Dark) -->
                @if($collection->products->isNotEmpty())
                    <div class="px-6 md:px-12">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-12">
                            @foreach($collection->products as $product)
                                <x-product-card :product="$product" :dark="true" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

        {{-- ============================================================
             LAYOUT VARIANT 2: Centered Text + Wide Landscape Banner
        ============================================================ --}}
        @elseif($layoutVariant === 2)
            <section class="py-16 md:py-24 mb-16 md:mb-24">
                <!-- Editorial Hero -->
                <div class="max-w-[1200px] mx-auto px-6 mb-16 md:mb-20 text-center">
                    <span class="text-[10px] uppercase tracking-[0.5em] text-brand-gray-light block mb-6">Collection {{ $collectionNumber }}</span>
                    <h2 class="text-4xl md:text-6xl lg:text-7xl font-serif font-normal tracking-tight mb-8 uppercase">
                        {{ $collection->name }}
                    </h2>
                    @if($collectionImage)
                        <div class="aspect-[21/9] w-full overflow-hidden mb-12 bg-brand-gray">
                            <img src="{{ $collectionImage }}"
                                 alt="{{ $collection->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    @if($collection->description)
                        <p class="text-xs uppercase tracking-[0.3em] text-brand-gray-medium max-w-2xl mx-auto">
                            {{ $collection->description }}
                        </p>
                    @endif
                </div>

                <!-- Product Grid -->
                @if($collection->products->isNotEmpty())
                    <div class="px-6 md:px-12">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-4 gap-y-12">
                            @foreach($collection->products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

        {{-- ============================================================
             LAYOUT VARIANT 3: Header with "View All" + Wide Banner
        ============================================================ --}}
        @else
            <section class="py-16 md:py-24 border-t border-brand-gray-border mb-16 md:mb-24">
                <div class="px-6 md:px-12 mb-16">
                    <!-- Header Row -->
                    <div class="flex justify-between items-end mb-12">
                        <div>
                            <span class="text-[10px] tracking-[0.5em] uppercase text-brand-gray-light">Collection {{ $collectionNumber }}</span>
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-normal uppercase mt-2">
                                {{ $collection->name }}
                            </h2>
                        </div>
                        <a href="{{ route('collections.show', $collection->slug) }}"
                           class="text-[11px] font-bold tracking-[0.3em] uppercase border-b border-brand-black pb-1 hover:opacity-60 transition-opacity duration-200">
                            ดูทั้งหมด
                        </a>
                    </div>

                    <!-- Wide Banner -->
                    @if($collectionImage)
                        <div class="aspect-[21/9] w-full overflow-hidden bg-brand-gray">
                            <img src="{{ $collectionImage }}"
                                 alt="{{ $collection->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                </div>

                <!-- Product Grid -->
                @if($collection->products->isNotEmpty())
                    <div class="px-6 md:px-12">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-x-4 gap-y-12">
                            @foreach($collection->products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif

    @empty
        <section class="py-20 text-center">
            <p class="text-brand-gray-medium">ยังไม่มีคอลเล็คชัน</p>
        </section>
    @endforelse

    {{-- ============================================================
         QUOTE SECTION
    ============================================================ --}}
    @if($quote)
        <section class="py-24 md:py-32 px-4 max-w-4xl mx-auto text-center">
            <span class="text-[10px] uppercase tracking-[0.5em] text-brand-gray-light block mb-10">CHOMIN</span>
            <blockquote class="text-2xl md:text-4xl lg:text-5xl font-serif italic leading-snug">
                {{ $quote }}
            </blockquote>
        </section>
    @endif

</x-layouts.shop>
