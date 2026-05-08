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

    {{-- ═══════════════════════════════════════════
         SECTION 1: HERO
    ═══════════════════════════════════════════ --}}
    @if($heroCollection)
        <section class="hero-cinematic bg-brand-black" aria-label="Hero banner">
            @if($heroImage)
                <div class="absolute inset-0 hero-mask">
                    <img src="{{ $heroImage }}"
                         alt="{{ $heroCollection->name }}"
                         class="w-full h-full object-cover"
                         fetchpriority="high">
                    <div class="absolute inset-0 bg-brand-black/40"></div>
                </div>
            @endif

            <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none" aria-hidden="true">
                <h1 class="hero-title-reveal font-serif uppercase leading-[0.85] text-white text-center whitespace-nowrap"
                    style="font-size: clamp(3rem, 15vw, 12rem);">
                    CHO.MIN
                </h1>
            </div>

            <div class="absolute bottom-0 left-0 right-0 z-10 px-6 md:px-12 pb-6 md:pb-8 flex items-end justify-between">
                <div class="hero-sub-reveal hidden sm:block">
                    <span class="text-white/50 text-xs tracking-[0.15em] uppercase block">
                        Design Your Own Shirt
                    </span>
                </div>

                <div class="hero-sub-reveal flex flex-col items-center gap-3 mx-auto sm:mx-0">
                    <span class="text-white/40 text-xs tracking-[0.15em] uppercase">Scroll</span>
                    <div class="scroll-hint-line text-white/40" aria-hidden="true"></div>
                </div>

                <div class="hero-sub-reveal text-right hidden sm:block">
                    <span class="text-white/50 text-xs tracking-[0.15em] uppercase block">
                        50+ Colors &mdash; XS to 6XL
                    </span>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         SECTION 2: HORIZONTAL PRODUCT GALLERY
    ═══════════════════════════════════════════ --}}
    @if($newArrivals->isNotEmpty())
        <section class="pt-16 pb-12 md:pt-24 md:pb-16" aria-label="สินค้ามาใหม่">
            <div class="px-6 md:px-12 mb-10 md:mb-14">
                <div class="flex justify-between items-end">
                    <div class="reveal" data-reveal>
                        <span class="text-xs tracking-[0.2em] uppercase text-brand-gray-light block mb-3">New Arrivals</span>
                        <h2 class="font-serif uppercase editorial-title leading-[0.9]"
                            style="font-size: clamp(2rem, 5vw, 3.5rem);">
                            สินค้ามาใหม่
                        </h2>
                    </div>
                    <a href="{{ route('shop.index') }}"
                       class="reveal text-xs tracking-[0.15em] uppercase hover:opacity-50 transition-opacity focus:outline-none focus:underline focus:underline-offset-4"
                       data-reveal>
                        ดูทั้งหมด
                    </a>
                </div>
            </div>

            <div class="h-gallery hide-scrollbar" data-drag-scroll>
                <div class="flex-shrink-0 w-[2vw] md:w-[6vw]" aria-hidden="true"></div>

                @foreach($newArrivals as $product)
                    @php
                        $primaryImage = $product->primaryImage ?? $product->images->first();
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}"
                       class="h-gallery-item w-[70vw] md:w-[30vw] group focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                       aria-label="{{ $product->localized_name }} — ฿{{ number_format($product->display_price) }}">
                        <div class="aspect-[3/4] bg-brand-gray overflow-hidden mb-4">
                            @if($primaryImage)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($primaryImage->image_path) }}"
                                     alt="{{ $product->localized_name }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @endif
                        </div>
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h3 class="text-sm uppercase tracking-[0.05em] leading-snug max-w-[220px]">{{ $product->localized_name }}</h3>
                                @if($product->variants && $product->variants->unique('color')->count() > 1)
                                    <span class="text-xs text-brand-gray-light tracking-[0.1em] uppercase mt-1.5 block">
                                        {{ $product->variants->unique('color')->count() }} สี
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-brand-gray-dark whitespace-nowrap">฿{{ number_format($product->display_price) }}</span>
                        </div>
                    </a>
                @endforeach

                <div class="flex-shrink-0 w-[2vw] md:w-[6vw]" aria-hidden="true"></div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         SECTION 3: BRAND EDITORIAL
    ═══════════════════════════════════════════ --}}
    <section class="sticky-editorial md:min-h-[100vh] py-16 md:py-0" aria-label="เกี่ยวกับแบรนด์">
        <div class="sticky-editorial-text px-6 md:px-16 order-2 md:order-1 pt-10 md:pt-0">
            <div class="max-w-xl">
                <span class="text-xs tracking-[0.2em] uppercase text-brand-gray-light block mb-8 reveal" data-reveal>
                    The Brand
                </span>

                <div class="mb-10 reveal" data-reveal>
                    <h2 class="font-serif uppercase editorial-title leading-[0.9]"
                        style="font-size: clamp(2rem, 4vw, 3rem);">
                        สไตล์<br>
                        ไม่ควร<br>
                        ถูกจำกัด
                    </h2>
                </div>

                <div class="space-y-4 text-base text-brand-gray-dark leading-[1.75] max-w-[50ch] reveal" data-reveal>
                    <p>เราออกแบบเชิ้ตให้คุณเป็นคนกำหนดเอง เลือกได้มากถึง 50 สี ตั้งแต่โทนมินิมอล คลาสสิก ไปจนถึงเฉดจัดจ้านสายแฟชั่น</p>
                    <p>เนื้อผ้าคุณภาพสัมผัสนุ่ม ใส่สบายและดูแพงอย่างเป็นธรรมชาติ</p>
                </div>

                <a href="{{ route('about') }}"
                   class="inline-block mt-10 text-xs tracking-[0.15em] uppercase border-b border-brand-black pb-1 hover:opacity-50 transition-opacity focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-4 reveal"
                   data-reveal>
                    เรื่องของเรา
                </a>
            </div>
        </div>

        <div class="order-1 md:order-2">
            <div class="overflow-hidden reveal" data-reveal>
                <img src="{{ asset('images/fb-posts/product-1.jpg') }}"
                     alt="CHO.MIN — คู่รักใส่เชิ้ต"
                     class="w-full aspect-[4/5] md:aspect-[3/4] object-cover">
            </div>
            <div class="overflow-hidden mt-4 reveal" data-reveal>
                <img src="{{ asset('images/fb-posts/product-3.jpg') }}"
                     alt="CHO.MIN — เชิ้ตหลากสี"
                     class="w-full aspect-[4/3] object-cover">
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         SECTION 4: FEATURED COLLECTIONS
    ═══════════════════════════════════════════ --}}
    @if($featuredCollections->isNotEmpty())
        <section class="pb-8 md:pb-12 px-6 md:px-12 pt-16 md:pt-24" aria-label="คอลเล็คชัน">
            <div class="max-w-6xl mx-auto">
                <div class="mb-12 md:mb-14 reveal" data-reveal>
                    <span class="text-xs tracking-[0.2em] uppercase text-brand-gray-light block mb-3">Collections</span>
                    <h2 class="font-serif uppercase editorial-title leading-[0.9]"
                        style="font-size: clamp(2rem, 5vw, 3.5rem);">
                        คอลเล็คชัน
                    </h2>
                </div>

                @foreach($collectionPairs as $pair)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-5 {{ !$loop->last ? 'mb-4 md:mb-5' : '' }}">
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
                               class="collection-card group relative block aspect-[4/5] overflow-hidden bg-brand-gray focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 reveal {{ $loop->first ? 'md:col-span-7' : 'md:col-span-5' }}"
                               data-reveal
                               aria-label="{{ $collection->localized_name }}">
                                @if($colImage)
                                    <img src="{{ $colImage }}"
                                         alt="{{ $collection->localized_name }}"
                                         class="w-full h-full object-cover transition-transform duration-700"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-brand-gray-light text-sm tracking-[0.1em] uppercase">{{ $collection->localized_name }}</span>
                                    </div>
                                @endif

                                <div class="card-overlay absolute inset-0 bg-gradient-to-t from-brand-black/60 via-brand-black/10 to-transparent opacity-60"></div>

                                <div class="card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                    <h3 class="text-white text-base md:text-lg uppercase tracking-[0.05em] mb-1">
                                        {{ $collection->localized_name }}
                                    </h3>
                                    @if($collection->localized_description)
                                        <p class="text-white/60 text-xs tracking-[0.1em] mb-5">
                                            {{ \Illuminate\Support\Str::limit($collection->localized_description, 60) }}
                                        </p>
                                    @endif
                                    <span class="card-btn text-white text-xs tracking-[0.1em] uppercase underline underline-offset-4 decoration-white/40" aria-hidden="true">
                                        สำรวจ
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         SECTION 5: LOOKBOOK
    ═══════════════════════════════════════════ --}}
    <section class="px-6 md:px-12 pt-12 md:pt-16 pb-24 md:pb-32" aria-label="Lookbook">
        <div class="max-w-6xl mx-auto">
            <div class="lookbook-grid">
                <a href="{{ route('shop.index') }}" class="group block reveal focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2" data-reveal
                   aria-label="Pinstripe Collection">
                    <div class="aspect-[3/4] md:aspect-auto md:h-full bg-brand-gray overflow-hidden">
                        <img src="{{ asset('images/fb-posts/pinstripe-1.jpg') }}"
                             alt="Pinstripe Collection — เชิ้ตลายทาง"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                    </div>
                </a>

                <a href="{{ route('shop.index') }}" class="group block reveal focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2" data-reveal
                   aria-label="เชิ้ตลาย Pinstripe">
                    <div class="aspect-[4/3] bg-brand-gray overflow-hidden">
                        <img src="{{ asset('images/fb-posts/pinstripe-2.jpg') }}"
                             alt="เชิ้ตลาย Pinstripe — เรียบหรูทุกโอกาส"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                    </div>
                    <h3 class="mt-4 text-sm uppercase tracking-[0.05em]">Pinstripe</h3>
                    <p class="text-xs text-brand-gray-light tracking-[0.1em] mt-1">เรียบหรูทุกโอกาส</p>
                </a>

                <a href="{{ route('shop.index') }}" class="group block reveal focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2" data-reveal
                   aria-label="Everyday Essentials">
                    <div class="aspect-[4/3] bg-brand-gray overflow-hidden">
                        <img src="{{ asset('images/fb-posts/product-3.jpg') }}"
                             alt="CHO.MIN Everyday Essentials"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">
                    </div>
                    <h3 class="mt-4 text-sm uppercase tracking-[0.05em]">Everyday Essentials</h3>
                    <p class="text-xs text-brand-gray-light tracking-[0.1em] mt-1">ใส่ได้ทุกวัน สวยทุกโอกาส</p>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         SECTION 6: CLOSING CTA
    ═══════════════════════════════════════════ --}}
    <section class="cta-fullscreen bg-brand-black px-6" aria-label="Call to action">
        <div class="reveal" data-reveal>
            <h2 class="font-serif uppercase editorial-title leading-[0.85] text-white text-center"
                style="font-size: clamp(2.5rem, 10vw, 8rem);">
                เสื้อผ้า<br>
                ที่เป็นคุณ
            </h2>
        </div>

        <a href="{{ route('shop.index') }}"
           class="mt-12 md:mt-16 inline-block text-white text-xs tracking-[0.15em] uppercase underline underline-offset-8 decoration-white/40 hover:decoration-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand-black reveal"
           data-reveal>
            ช้อปเลย &rarr;
        </a>
    </section>

</x-layouts.shop>
