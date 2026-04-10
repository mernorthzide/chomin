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

    {{-- HERO BANNER --}}
    @if($heroCollection)
        <section class="relative w-full h-[70vh] md:h-[90vh] overflow-hidden">
            @if($heroImage)
                <img src="{{ $heroImage }}"
                     alt="{{ $heroCollection->name }}"
                     class="w-full h-full object-cover ken-burns"
                     fetchpriority="high">
            @else
                <div class="w-full h-full bg-brand-gray flex items-center justify-center">
                    <span class="text-brand-gray-light text-sm tracking-widest uppercase">{{ $heroCollection->name }}</span>
                </div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-brand-black/60 via-brand-black/15 to-transparent"></div>

            <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center px-6 z-10 hero-text-reveal">
                <span class="text-[10px] md:text-xs tracking-[0.5em] uppercase mb-6 opacity-70">
                    Design Your Own Shirt
                </span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif uppercase editorial-title leading-none mb-8">
                    {{ $heroCollection->name }}
                </h1>
                <p class="text-xs md:text-sm tracking-widest uppercase opacity-60 mb-12 max-w-lg">
                    เลือกได้มากถึง 50 สี ดีไซน์เรียบ แต่ซ่อนรายละเอียดที่ทำให้แตกต่าง
                </p>
                <a href="{{ route('shop.index') }}"
                   class="bg-white text-brand-black px-10 py-4 text-[10px] font-bold tracking-[0.25em] uppercase hover:bg-brand-black hover:text-white transition-colors duration-500 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand-black/50">
                    ช้อปเลย
                </a>
            </div>
        </section>
    @endif

    {{-- NEW ARRIVALS --}}
    @if($newArrivals->isNotEmpty())
        <section class="py-16 md:py-24 reveal" data-reveal>
            <div class="px-6 md:px-12 flex justify-between items-end mb-10 border-b border-brand-gray-border pb-4">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold uppercase tracking-wide">สินค้ามาใหม่</h2>
                    <p class="text-xs text-brand-gray-medium tracking-widest uppercase mt-1">
                        ค้นพบสินค้าล่าสุดจากคอลเล็คชันของเรา
                    </p>
                </div>
                <a href="{{ route('shop.index') }}"
                   class="text-xs font-bold tracking-[0.2em] uppercase hover:opacity-60 transition-opacity focus:outline-none focus:underline">
                    ดูทั้งหมด →
                </a>
            </div>

            <div class="pl-6 md:pl-12 relative">
                <div class="flex gap-5 overflow-x-auto hide-scrollbar pb-4 reveal-stagger" data-reveal>
                    @foreach($newArrivals as $product)
                        <div class="flex-shrink-0 w-[200px] md:w-[260px]">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
                <!-- Scroll hint -->
                <div class="absolute right-0 top-0 bottom-4 w-16 bg-gradient-to-l from-white to-transparent pointer-events-none md:block hidden"></div>
            </div>
        </section>
    @endif

    {{-- FEATURED COLLECTIONS --}}
    @if($featuredCollections->isNotEmpty())
        <section class="py-16 md:py-24 px-6 md:px-12">
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
                           class="collection-card group relative block aspect-[4/5] overflow-hidden bg-brand-gray focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                            @if($colImage)
                                <img src="{{ $colImage }}"
                                     alt="{{ $collection->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-brand-gray-light text-sm tracking-widest uppercase">{{ $collection->name }}</span>
                                </div>
                            @endif

                            <div class="card-overlay absolute inset-0 bg-gradient-to-t from-brand-black/60 via-brand-black/10 to-transparent opacity-60"></div>

                            <div class="card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                <h3 class="text-white text-xl md:text-2xl font-bold uppercase tracking-wide mb-1">
                                    {{ $collection->name }}
                                </h3>
                                @if($collection->description)
                                    <p class="text-white/70 text-xs tracking-widest uppercase mb-5">
                                        {{ \Illuminate\Support\Str::limit($collection->description, 60) }}
                                    </p>
                                @endif
                                <span class="card-btn inline-block bg-white text-brand-black px-6 py-2.5 text-xs font-bold tracking-[0.2em] uppercase">
                                    สำรวจ
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </section>
    @endif

    {{-- BRAND STORY — editorial, no price --}}
    <section class="py-16 md:py-24 px-6 md:px-16 overflow-hidden">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-20 items-center">
            <div class="reveal" data-reveal>
                <img src="{{ asset('images/fb-posts/premium-shirt-ad.jpg') }}"
                     alt="CHO.MIN Premium Shirt"
                     class="w-full aspect-[4/5] object-cover"
                     loading="lazy">
            </div>
            <div class="reveal" data-reveal>
                <span class="text-[10px] tracking-[0.5em] uppercase text-brand-gray-light block mb-6">เกี่ยวกับ CHO.MIN</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif uppercase editorial-title leading-none mb-8">
                    สไตล์<br>ไม่ควร<br>ถูกจำกัด
                </h2>
                <div class="space-y-4 text-sm text-brand-gray-dark leading-relaxed max-w-md">
                    <p>เราออกแบบเชิ้ตให้คุณเป็นคนกำหนดเอง เลือกได้มากถึง 50 สี ตั้งแต่โทนมินิมอล คลาสสิก ไปจนถึงเฉดจัดจ้านสายแฟชั่น</p>
                    <p>เนื้อผ้าคุณภาพสัมผัสนุ่ม ใส่สบายและดูแพงอย่างเป็นธรรมชาติ ดีไซน์เรียบ แต่ซ่อนรายละเอียดที่ทำให้แตกต่างอย่างมีชั้นเชิง</p>
                </div>
                <a href="{{ route('shop.index') }}"
                   class="inline-block mt-10 bg-brand-black text-white px-10 py-4 text-[10px] font-bold tracking-[0.25em] uppercase hover:bg-brand-gray-dark transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                    ดูสินค้าทั้งหมด
                </a>
            </div>
        </div>
    </section>

    {{-- PINSTRIPE & CARE GUIDE --}}
    <section class="py-16 md:py-24 px-6 md:px-12">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 reveal-stagger" data-reveal>
            <a href="{{ route('shop.index') }}" class="group block img-hover-zoom">
                <div class="aspect-[3/4] bg-neutral-100 overflow-hidden">
                    <img src="{{ asset('images/fb-posts/pinstripe-1.jpg') }}"
                         alt="Pinstripe Collection"
                         class="w-full h-full object-cover"
                         loading="lazy">
                </div>
                <h3 class="mt-5 text-sm font-bold uppercase tracking-wide">Pinstripe Collection</h3>
                <p class="text-[10px] text-brand-gray-light tracking-[0.3em] uppercase mt-2">คอลเลคชั่นใหม่</p>
            </a>
            <a href="{{ route('shop.index') }}" class="group block img-hover-zoom md:mt-12">
                <div class="aspect-[3/4] bg-neutral-100 overflow-hidden">
                    <img src="{{ asset('images/fb-posts/pinstripe-2.jpg') }}"
                         alt="เชิ้ตลาย Pinstripe"
                         class="w-full h-full object-cover"
                         loading="lazy">
                </div>
                <h3 class="mt-5 text-sm font-bold uppercase tracking-wide">เชิ้ตลาย Pinstripe</h3>
                <p class="text-[10px] text-brand-gray-light tracking-[0.3em] uppercase mt-2">เรียบหรูทุกโอกาส</p>
            </a>
            <div class="img-hover-zoom">
                <div class="aspect-[3/4] bg-neutral-100 overflow-hidden">
                    <img src="{{ asset('images/fb-posts/care-guide.jpg') }}"
                         alt="วิธีดูแลเชิ้ต CHO.MIN"
                         class="w-full h-full object-cover"
                         loading="lazy">
                </div>
                <h3 class="mt-5 text-sm font-bold uppercase tracking-wide">วิธีดูแลเชิ้ต CHO.MIN</h3>
                <p class="text-[10px] text-brand-gray-light tracking-[0.3em] uppercase mt-2">ให้ใส่ได้นาน ทรงยังสวย</p>
            </div>
        </div>
    </section>

    {{-- BOTTOM BANNER --}}
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
        <section class="relative w-full h-[50vh] md:h-[65vh] overflow-hidden">
            @if($bottomImage)
                <img src="{{ $bottomImage }}"
                     alt="{{ $bottomCollection->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            @else
                <div class="w-full h-full bg-brand-gray flex items-center justify-center">
                    <span class="text-brand-gray-light text-sm tracking-widest uppercase">{{ $bottomCollection->name }}</span>
                </div>
            @endif

            <div class="absolute inset-0 bg-brand-black/40"></div>

            <div class="absolute inset-0 flex flex-col items-center justify-end pb-14 md:pb-20 px-6 z-10">
                <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif uppercase editorial-title leading-none text-center mb-8">
                    {{ $bottomCollection->name }}
                </h2>
                <a href="{{ route('collections.show', $bottomCollection->slug) }}"
                   class="border border-white/60 text-white px-10 py-4 text-[10px] font-bold tracking-[0.25em] uppercase hover:bg-white hover:text-brand-black transition-colors duration-500 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand-black/50">
                    ดูคอลเล็คชัน
                </a>
            </div>
        </section>
    @endif

</x-layouts.shop>
