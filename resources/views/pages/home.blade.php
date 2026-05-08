<x-layouts.shop :title="'CHO.MIN | Design Your Own Shirt'">

    @php
        $heroCollection = $collections->first();
        $featuredCollections = $collections;
        $heroImage = null;

        if ($heroCollection) {
            if ($heroCollection->banner_image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->banner_image);
            } elseif ($heroCollection->image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->image);
            }
        }

        $newArrivals = $heroCollection ? $heroCollection->products : collect();
        $heroColors = $newArrivals
            ->flatMap(fn ($product) => $product->colors)
            ->unique(fn ($color) => $color->slug ?: $color->name)
            ->take(12)
            ->values();
    @endphp

    @if($heroCollection)
        <section class="campaign-hero bg-white border-b border-brand-gray-border" aria-label="CHOMIN campaign">
            <a href="{{ route('collections.show', $heroCollection->slug) }}" class="campaign-hero-link group">
                @if($heroImage)
                    <img src="{{ $heroImage }}"
                         alt="{{ $heroCollection->localized_name }}"
                         class="campaign-hero-image"
                         fetchpriority="high">
                @endif

                <div class="campaign-hero-copy">
                    <p class="text-xs uppercase tracking-[0.16em] text-brand-gray-medium">CM Classic</p>
                    <h1 class="mt-3 font-serif uppercase leading-none text-brand-black campaign-hero-title">
                        Design Your Own Shirt
                    </h1>
                    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs uppercase tracking-[0.14em] text-brand-gray-dark">
                        <span>50+ สี</span>
                        <span>XS-6XL</span>
                        <span>Collar / Cuff / Pocket</span>
                    </div>
                </div>

                <div class="campaign-hero-cta">
                    <span class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                        Shop CM Classic
                    </span>
                </div>
            </a>
        </section>
    @endif

    @if($featuredCollections->isNotEmpty())
        <section class="bg-white border-b border-brand-gray-border" aria-label="Campaign collections">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 border-t border-brand-gray-border">
                @foreach($featuredCollections as $collection)
                    @php
                        $collectionImage = $collection->banner_image
                            ? \Illuminate\Support\Facades\Storage::url($collection->banner_image)
                            : ($collection->image ? \Illuminate\Support\Facades\Storage::url($collection->image) : null);
                    @endphp
                    <a href="{{ route('collections.show', $collection->slug) }}"
                       class="campaign-tile group border-b border-r border-brand-gray-border focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-inset">
                        <div class="aspect-[4/5] bg-brand-gray overflow-hidden">
                            @if($collectionImage)
                                <img src="{{ $collectionImage }}"
                                     alt="{{ $collection->localized_name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="font-serif text-6xl text-brand-gray-border">{{ strtoupper(substr($collection->localized_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-h-[72px] px-3 py-3 flex items-center justify-between gap-3">
                            <h2 class="text-xs uppercase tracking-[0.08em] leading-snug">{{ $collection->localized_name }}</h2>
                            <span class="text-[10px] uppercase tracking-[0.12em] text-brand-gray-light whitespace-nowrap">
                                {{ $collection->products_count ?? $collection->products->count() }} items
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="px-6 md:px-12 py-16 md:py-24 bg-white border-b border-brand-gray-border" aria-label="Build Your Shirt">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            <div class="lg:col-span-5">
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Build Your Shirt</p>
                <h2 class="font-serif uppercase leading-none text-brand-black" style="font-size: clamp(2.4rem, 7vw, 6.5rem);">
                    เชิ้ตที่<br>เป็นคุณ
                </h2>
            </div>
            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-3 border border-brand-gray-border">
                <a href="{{ route('color-library') }}" class="p-6 md:p-7 border-b md:border-b-0 md:border-r border-brand-gray-border group">
                    <span class="block text-4xl font-serif leading-none">50+</span>
                    <h3 class="mt-4 text-xs uppercase tracking-[0.14em]">สีให้เลือก</h3>
                    <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">เลือกโทนทำงาน คลาสสิก หรือสีชัดสำหรับวันพิเศษ</p>
                    @if($heroColors->isNotEmpty())
                        <div class="mt-5 flex flex-wrap gap-1.5">
                            @foreach($heroColors as $color)
                                <span class="h-5 w-5 rounded-full border border-brand-gray-border"
                                      style="background-color: {{ $color->color_code ?? '#eeeeee' }}"
                                      title="{{ $color->localized_name }}"></span>
                            @endforeach
                        </div>
                    @endif
                </a>
                <a href="{{ route('pages.size-guide') }}" class="p-6 md:p-7 border-b md:border-b-0 md:border-r border-brand-gray-border group">
                    <span class="block text-4xl font-serif leading-none">XS-6XL</span>
                    <h3 class="mt-4 text-xs uppercase tracking-[0.14em]">ไซส์ครอบคลุม</h3>
                    <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">ทำให้การหาเชิ้ตพอดีตัวง่ายขึ้น ตั้งแต่ตัวเล็กถึงพลัสไซส์</p>
                </a>
                <a href="{{ route('pages.member') }}" class="p-6 md:p-7 group">
                    <span class="block text-4xl font-serif leading-none">3</span>
                    <h3 class="mt-4 text-xs uppercase tracking-[0.14em]">รายละเอียดที่เลือกได้</h3>
                    <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">เลือกคอเสื้อ ปลายแขน และกระเป๋าให้เข้ากับการใช้งาน</p>
                </a>
            </div>
        </div>
    </section>

    @if($newArrivals->isNotEmpty())
        <section class="py-14 md:py-20 bg-white" aria-label="New arrivals">
            <div class="px-6 md:px-12 mb-8 md:mb-10 flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-3">New Arrivals</p>
                    <h2 class="font-serif uppercase leading-none text-4xl md:text-6xl">สินค้าใหม่</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden sm:inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1 hover:opacity-60">
                    View all
                </a>
            </div>

            <div class="product-rail hide-scrollbar" data-drag-scroll>
                @foreach($newArrivals as $product)
                    <div class="product-rail-item">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-2 border-t border-b border-brand-gray-border bg-white" aria-label="CHOMIN editorial">
        <div class="order-2 md:order-1 px-6 md:px-12 py-14 md:py-20 flex items-center">
            <div class="max-w-xl">
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-6">The Brand</p>
                <h2 class="font-serif uppercase leading-none text-4xl md:text-6xl">Style should fit real life.</h2>
                <div class="mt-8 space-y-4 text-sm md:text-base text-brand-gray-dark leading-relaxed">
                    <p>CHOMIN ทำเชิ้ตให้เลือกได้มากกว่าแค่สี เราให้คุณเลือกสัดส่วน รายละเอียด และโทนที่เข้ากับวันที่ต้องใส่จริง</p>
                    <p>โครงเว็บใหม่นี้วางสินค้าเป็นพระเอก ให้ภาพ สี ไซส์ และทางลัดการซื้อช่วยเล่าเรื่องแทนคำขายยาว ๆ</p>
                </div>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Shop all</a>
                    <a href="{{ route('stories.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Stories</a>
                </div>
            </div>
        </div>
        <div class="order-1 md:order-2 grid grid-cols-2 border-b md:border-b-0 md:border-l border-brand-gray-border">
            <img src="{{ asset('images/fb-posts/product-1.jpg') }}"
                 alt="CHO.MIN styling"
                 class="h-full min-h-[360px] w-full object-cover border-r border-brand-gray-border"
                 loading="lazy">
            <img src="{{ asset('images/fb-posts/pinstripe-1.jpg') }}"
                 alt="CHO.MIN pinstripe shirt"
                 class="h-full min-h-[360px] w-full object-cover"
                 loading="lazy">
        </div>
    </section>

    <section class="bg-brand-black text-white px-6 md:px-12 py-16 md:py-24 text-center" aria-label="Call to action">
        <p class="text-xs uppercase tracking-[0.2em] text-white/50 mb-8">Free shipping / 30 day exchange / Member points</p>
        <h2 class="font-serif uppercase leading-none mx-auto max-w-5xl" style="font-size: clamp(2.8rem, 9vw, 8rem);">
            Start with the shirt.
        </h2>
        <a href="{{ route('shop.index') }}"
           class="mt-10 inline-block text-xs uppercase tracking-[0.18em] border-b border-white pb-1 hover:text-white/70">
            ช้อปเลย
        </a>
    </section>

</x-layouts.shop>
