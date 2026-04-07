<x-layouts.shop>

    @php
        $heroCollection = $collections->first();
        $featuredCollections = $collections->skip(1);

        $heroImage = null;
        if ($heroCollection) {
            if ($heroCollection->banner_image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->banner_image);
            } elseif ($heroCollection->image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->image);
            }
        }

        $newArrivals = $heroCollection ? $heroCollection->products : collect();
        $collectionPairs = $featuredCollections->chunk(2);
    @endphp

    {{-- ============================================================
         SECTION 1: FULL-WIDTH HERO BANNER with grain + parallax
    ============================================================ --}}
    @if($heroCollection)
        <section class="relative w-full h-[70vh] md:h-[85vh] overflow-hidden parallax-hero grain-overlay">
            @if($heroImage)
                <img src="{{ $heroImage }}"
                     alt="{{ $heroCollection->name }}"
                     class="w-full h-full object-cover scale-105"
                     data-parallax>
            @else
                <div class="w-full h-full bg-brand-gray"></div>
            @endif

            <!-- Gradient overlay with vignette -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/10"></div>
            <div class="absolute inset-0" style="box-shadow: inset 0 0 150px rgba(0,0,0,0.3);"></div>

            <!-- Centered text with staggered reveal -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center px-6 z-10 hero-text-reveal">
                <span class="text-[10px] md:text-[11px] tracking-[0.5em] uppercase mb-4 opacity-90">
                    {{ $heroCollection->description ?? 'Limited Collection' }}
                </span>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-serif uppercase editorial-title leading-none mb-6">
                    {{ $heroCollection->name }}
                </h1>
                <p class="text-[11px] md:text-xs tracking-widest uppercase opacity-80 mb-10 max-w-md">
                    คอลเล็คชันล่าสุดจาก CHO.MIN พร้อมให้คุณแล้ววันนี้
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('shop.index') }}"
                       class="bg-brand-black text-white px-8 py-3.5 text-[10px] font-bold tracking-[0.3em] uppercase hover:bg-brand-gray-dark transition-all duration-300 hover:scale-105">
                        ช้อปเลย
                    </a>
                    <a href="{{ route('collections.show', $heroCollection->slug) }}"
                       class="bg-white text-brand-black px-8 py-3.5 text-[10px] font-bold tracking-[0.3em] uppercase hover:bg-brand-gray transition-all duration-300 hover:scale-105">
                        ดูคอลเล็คชัน
                    </a>
                </div>
            </div>

            <!-- Decorative corner lines -->
            <div class="absolute top-8 left-8 w-16 h-16 border-t border-l border-white/20 z-10 hidden md:block"></div>
            <div class="absolute bottom-8 right-8 w-16 h-16 border-b border-r border-white/20 z-10 hidden md:block"></div>
        </section>
    @endif

    {{-- ============================================================
         SECTION 2: NEW ARRIVALS — Horizontal Product Scroll
    ============================================================ --}}
    @if($newArrivals->isNotEmpty())
        <section class="py-14 md:py-20 reveal" data-reveal>
            <!-- Section Header -->
            <div class="px-6 md:px-12 mb-3">
                <h2 class="text-xl md:text-2xl font-bold uppercase tracking-wide">สินค้ามาใหม่</h2>
                <p class="text-[11px] text-brand-gray-medium tracking-widest uppercase mt-1">
                    ค้นพบสินค้าล่าสุดจากคอลเล็คชันของเรา
                </p>
            </div>

            <!-- Category tabs + Shop link -->
            <div class="px-6 md:px-12 flex justify-between items-center mb-8 border-b border-brand-gray-border pb-4">
                <div class="flex gap-6 text-[11px] uppercase tracking-widest font-semibold">
                    <span class="border-b-2 border-brand-black pb-1 cursor-pointer">ทั้งหมด</span>
                    <span class="text-brand-gray-light hover:text-brand-black transition-colors cursor-pointer">เสื้อ</span>
                </div>
                <a href="{{ route('shop.index') }}"
                   class="text-[11px] font-bold tracking-[0.2em] uppercase hover:opacity-60 transition-opacity">
                    ดูทั้งหมด
                </a>
            </div>

            <!-- Horizontal scroll product cards with stagger -->
            <div class="pl-6 md:pl-12">
                <div class="flex gap-4 overflow-x-auto hide-scrollbar pb-4 reveal-stagger" data-reveal>
                    @foreach($newArrivals as $product)
                        <div class="flex-shrink-0 w-[180px] md:w-[220px]">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================
         SECTION 4: TWO-COLUMN COLLECTION CARDS with hover effects
    ============================================================ --}}
    @if($featuredCollections->isNotEmpty())
        <section class="py-14 md:py-20 px-6 md:px-12">
            @foreach($collectionPairs as $pair)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 {{ !$loop->last ? 'mb-4 md:mb-6' : '' }} reveal" data-reveal>
                    @foreach($pair as $collection)
                        @php
                            $colImage = null;
                            if ($collection->banner_image) {
                                $colImage = \Illuminate\Support\Facades\Storage::url($collection->banner_image);
                            } elseif ($collection->image) {
                                $colImage = \Illuminate\Support\Facades\Storage::url($collection->image);
                            }
                        @endphp
                        <a href="{{ route('collections.show', $collection->slug) }}"
                           class="collection-card group relative block aspect-[4/5] overflow-hidden bg-brand-gray">
                            @if($colImage)
                                <img src="{{ $colImage }}"
                                     alt="{{ $collection->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                     loading="lazy">
                            @endif

                            <!-- Gradient overlay -->
                            <div class="card-overlay absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-70"></div>

                            <!-- Grain on cards -->
                            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                                 style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E'); background-size: 128px;"></div>

                            <!-- Text overlay at bottom -->
                            <div class="card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                <h3 class="text-white text-xl md:text-2xl font-bold uppercase tracking-wide mb-1">
                                    {{ $collection->name }}
                                </h3>
                                @if($collection->description)
                                    <p class="text-white/70 text-[11px] tracking-widest uppercase mb-5">
                                        {{ \Illuminate\Support\Str::limit($collection->description, 60) }}
                                    </p>
                                @endif
                                <span class="card-btn inline-block bg-white text-brand-black px-6 py-2.5 text-[10px] font-bold tracking-[0.3em] uppercase">
                                    สำรวจ
                                </span>
                            </div>

                            <!-- Decorative corner -->
                            <div class="absolute top-5 right-5 w-10 h-10 border-t border-r border-white/0 group-hover:border-white/30 transition-all duration-500 z-10"></div>
                            <div class="absolute bottom-20 left-5 w-10 h-10 border-b border-l border-white/0 group-hover:border-white/30 transition-all duration-500 z-10 md:bottom-24"></div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </section>
    @endif

    {{-- ============================================================
         SECTION 5: FULL-WIDTH BOTTOM BANNER + TYPOGRAPHY
    ============================================================ --}}
    @if($collections->count() > 1)
        @php
            $bottomCollection = $collections->last();
            $bottomImage = null;
            if ($bottomCollection->banner_image) {
                $bottomImage = \Illuminate\Support\Facades\Storage::url($bottomCollection->banner_image);
            } elseif ($bottomCollection->image) {
                $bottomImage = \Illuminate\Support\Facades\Storage::url($bottomCollection->image);
            }
        @endphp
        <section class="relative w-full h-[50vh] md:h-[60vh] overflow-hidden grain-overlay">
            @if($bottomImage)
                <img src="{{ $bottomImage }}"
                     alt="{{ $bottomCollection->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            @else
                <div class="w-full h-full bg-brand-gray"></div>
            @endif

            <!-- Dark overlay -->
            <div class="absolute inset-0 bg-black/40"></div>

            <!-- Large typography overlay with floating effect -->
            <div class="absolute inset-0 flex flex-col items-center justify-end pb-12 md:pb-16 px-6 z-10">
                <div class="floating-type text-center cursor-default">
                    <span class="block text-white/40 text-3xl md:text-5xl lg:text-6xl font-serif uppercase editorial-title leading-tight transition-all duration-500">
                        Haute Couture
                    </span>
                    <span class="block text-white text-3xl md:text-5xl lg:text-6xl font-serif uppercase italic leading-tight transition-all duration-500">
                        Ready to Wear
                    </span>
                    <span class="block text-3xl md:text-5xl lg:text-6xl font-serif uppercase editorial-title leading-tight transition-all duration-500"
                          style="color: transparent; -webkit-text-stroke: 1px rgba(255,255,255,0.5);">
                        Iconic
                    </span>
                    <span class="block text-white/20 text-3xl md:text-5xl lg:text-6xl font-serif uppercase editorial-title leading-tight transition-all duration-500">
                        Summer Edit
                    </span>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================
         QUOTE SECTION with reveal
    ============================================================ --}}
    @if($quote)
        <section class="py-20 md:py-28 px-6 max-w-4xl mx-auto text-center reveal" data-reveal>
            <span class="text-[10px] uppercase tracking-[0.5em] text-brand-gray-light block mb-8">CHOMIN</span>
            <blockquote class="text-2xl md:text-4xl lg:text-5xl font-serif italic leading-snug">
                {{ $quote }}
            </blockquote>
            <!-- Decorative line -->
            <div class="w-12 h-px bg-brand-gray-light mx-auto mt-10"></div>
        </section>
    @endif

</x-layouts.shop>
