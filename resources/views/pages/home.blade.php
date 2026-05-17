<x-layouts.shop :title="'CHO.MIN | Design Your Own Shirt'">

    @php
        $heroCollection = $collections->first();
        $heroImage = null;
        $storageImage = fn (string $path): string => \Illuminate\Support\Facades\Storage::url($path);
        $campaignImages = [
            'customDetails' => $storageImage('products/chomin-imagen/custom-details.jpg'),
            'duoBox' => $storageImage('products/chomin-imagen/duo-box.jpg'),
            'careStudio' => $storageImage('products/chomin-imagen/care-studio.jpg'),
            'lifestyle' => $storageImage('products/chomin-imagen/lifestyle-editorial.jpg'),
        ];

        if ($heroCollection) {
            if ($heroCollection->banner_image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->banner_image);
            } elseif ($heroCollection->image) {
                $heroImage = \Illuminate\Support\Facades\Storage::url($heroCollection->image);
            }
        }

        $lineProducts = $lineProducts ?? collect();
        $heroColors = $lineProducts
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
                        <span>Special ฿999</span>
                        <span>DuoDeal ฿1,850</span>
                        <span>50+ สี</span>
                        <span>XS-6XL</span>
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

    @if($lineProducts->isNotEmpty())
        <section class="bg-white border-b border-brand-gray-border" aria-label="Shop by shirt line">
            <div class="px-6 md:px-12 py-8 border-t border-brand-gray-border flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-3">Shop by shirt line</p>
                    <h2 class="font-serif uppercase leading-none text-3xl md:text-5xl">เลือกจาก 5 ไลน์</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden sm:inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1 hover:opacity-60">
                    View all
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach($lineProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="px-6 md:px-12 py-16 md:py-24 bg-white border-b border-brand-gray-border" aria-label="Build Your Shirt">
        <div class="grid grid-cols-1 xl:grid-cols-12 border border-brand-gray-border bg-white">
            <div class="xl:col-span-4 p-7 md:p-10 flex items-end min-h-[340px]">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Build Your Shirt</p>
                    <h2 class="font-serif uppercase leading-none text-brand-black" style="font-size: clamp(2.4rem, 7vw, 6.5rem);">
                        เชิ้ตที่<br>เป็นคุณ
                    </h2>
                </div>
            </div>
            <div class="xl:col-span-4 border-t xl:border-t-0 xl:border-l border-brand-gray-border">
                <img src="{{ $campaignImages['customDetails'] }}"
                     alt="CHO.MIN custom collar cuff pocket details"
                     class="h-full min-h-[360px] w-full object-cover"
                     loading="lazy">
            </div>
            <div class="xl:col-span-4 grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-1 border-t xl:border-t-0 xl:border-l border-brand-gray-border">
                <a href="{{ route('color-library') }}" class="p-6 md:p-7 border-b sm:border-b-0 sm:border-r xl:border-r-0 xl:border-b border-brand-gray-border group">
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
                <a href="{{ route('pages.size-guide') }}" class="p-6 md:p-7 border-b sm:border-b-0 sm:border-r xl:border-r-0 xl:border-b border-brand-gray-border group">
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

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 border-b border-brand-gray-border bg-white" aria-label="Facebook campaign offers">
        <div class="border-b md:border-r border-brand-gray-border">
            <img src="{{ $campaignImages['duoBox'] }}"
                 alt="CHO.MIN DuoDeal gift-ready shirts"
                 class="h-full min-h-[320px] w-full object-cover"
                 loading="lazy">
        </div>
        <div class="px-6 md:px-10 py-12 md:py-16 border-b xl:border-r border-brand-gray-border">
            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-5">Special Price</p>
            <h2 class="font-serif uppercase leading-none text-5xl md:text-7xl">999</h2>
            <p class="mt-5 text-sm text-brand-gray-dark leading-relaxed">จากราคาเต็ม 1,790 บาท สำหรับ CM Classic Custom Shirt เลือกสี ไซส์ และรายละเอียดได้ครบ</p>
            <a href="{{ route('shop.index') }}" class="mt-8 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Shop special</a>
        </div>
        <div class="border-b md:border-b-0 md:border-r border-brand-gray-border">
            <img src="{{ $campaignImages['careStudio'] }}"
                 alt="CHO.MIN shirt care studio"
                 class="h-full min-h-[320px] w-full object-cover"
                 loading="lazy">
        </div>
        <div class="px-6 md:px-10 py-12 md:py-16 bg-brand-black text-white">
            <p class="text-xs uppercase tracking-[0.18em] text-white/50 mb-5">DuoDeal</p>
            <h2 class="font-serif uppercase leading-none text-4xl md:text-6xl">2 shirts<br>1,850</h2>
            <p class="mt-5 text-sm text-white/70 leading-relaxed">โปรคู่สุดคุ้มสำหรับเติมเชิ้ตคุณภาพเข้าตู้ เสื้อเนื้อนุ่ม ใส่สบาย เหมาะทั้งใช้เองและเป็นของขวัญ</p>
            <a href="https://line.me/R/ti/p/@chomin.th" target="_blank" rel="noopener" class="mt-8 inline-block text-xs uppercase tracking-[0.16em] border-b border-white pb-1">LINE @chomin.th</a>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-12 border-t border-b border-brand-gray-border bg-white" aria-label="CHOMIN editorial">
        <div class="order-2 lg:order-1 lg:col-span-5 px-6 md:px-12 py-14 md:py-20 flex items-center">
            <div class="max-w-xl">
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-6">The Brand</p>
                <h2 class="font-serif uppercase leading-none text-4xl md:text-6xl">Simple. Comfortable. Your Style.</h2>
                <div class="mt-8 space-y-4 text-sm md:text-base text-brand-gray-dark leading-relaxed">
                    <p>CHOMIN ทำเชิ้ตให้เลือกได้มากกว่าแค่สี เราให้คุณเลือกสัดส่วน รายละเอียด และโทนที่เข้ากับวันที่ต้องใส่จริง</p>
                    <p>เริ่มจากเชิ้ตดี ๆ สักตัว แล้วปรับรายละเอียดให้เข้ากับวิธีแต่งตัวของคุณในทุกวัน</p>
                </div>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Shop all</a>
                    <a href="{{ route('stories.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Stories</a>
                </div>
            </div>
        </div>
        <div class="order-1 lg:order-2 lg:col-span-7 border-b lg:border-b-0 lg:border-l border-brand-gray-border">
            <img src="{{ $campaignImages['lifestyle'] }}"
                 alt="CHO.MIN lifestyle editorial shirts"
                 class="h-full min-h-[360px] w-full object-cover"
                 loading="lazy">
        </div>
    </section>

    <section class="relative overflow-hidden bg-brand-black text-white px-6 md:px-12 py-16 md:py-24 text-center" aria-label="Call to action">
        <img src="{{ $campaignImages['lifestyle'] }}"
             alt=""
             class="absolute inset-0 h-full w-full object-cover opacity-35"
             loading="lazy">
        <div class="absolute inset-0 bg-brand-black/55"></div>
        <div class="relative z-10">
            <p class="text-xs uppercase tracking-[0.2em] text-white/60 mb-8">Free shipping / 30 day exchange / LINE @chomin.th</p>
            <h2 class="font-serif uppercase leading-none mx-auto max-w-5xl" style="font-size: clamp(2.8rem, 9vw, 8rem);">
                Define Your Elegance.
            </h2>
            <a href="{{ route('shop.index') }}"
               class="mt-10 inline-block text-xs uppercase tracking-[0.18em] border-b border-white pb-1 hover:text-white/70">
                ช้อปเลย
            </a>
        </div>
    </section>

    <x-instagram-feed />

</x-layouts.shop>
