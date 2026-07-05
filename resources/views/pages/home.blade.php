<x-layouts.shop :title="'CHO.MIN | Design Your Own Shirt'">

    @php
        $heroCollection = $collections->first();
        $heroImage = null;
        $storageImage = fn (string $path): string => \Illuminate\Support\Facades\Storage::url($path);
        $locale = app()->getLocale();
        $campaignImages = [
            'customDetails' => $storageImage('products/chomin-imagen/custom-details.jpg'),
            'duoBox' => $storageImage('products/chomin-imagen/duo-box.jpg'),
            'careStudio' => $storageImage('products/chomin-imagen/care-studio.jpg'),
            'lifestyle' => $storageImage('products/chomin-imagen/lifestyle-editorial.jpg'),
            'brandHero' => $storageImage('products/chomin-imagen/brand-hero.jpg'),
            'brandLifestyle' => $storageImage('products/chomin-imagen/brand-lifestyle.jpg'),
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

    <section class="bg-white border-b border-brand-gray-border" aria-label="CHO.MIN lookbook">
        <div class="px-6 py-10 md:py-14 flex justify-center">
            <img src="{{ $storageImage('products/chomin-imagen/from-client/models-lookbook-unisex.jpg') }}"
                 alt="{{ $locale === 'en' ? 'CHO.MIN unisex shirts — design your own in Premium Japanese Cotton' : 'เชิ้ต CHO.MIN ทรง unisex ออกแบบเองได้ ผ้า Premium Japanese Cotton' }}"
                 class="w-full max-w-md md:max-w-lg h-auto"
                 fetchpriority="high">
        </div>
    </section>

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
                    <p class="text-xs uppercase tracking-[0.16em] text-brand-gray-medium">{{ $heroCollection->localized_name }}</p>
                    <h1 class="mt-3 font-serif uppercase leading-none text-brand-black campaign-hero-title">
                        Design Your Own Shirt
                    </h1>
                    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs uppercase tracking-[0.14em] text-brand-gray-dark">
                        <span>฿1,190</span>
                        <span>50+ สี</span>
                        <span>S&ndash;XL</span>
                        <span>Premium Japanese Cotton</span>
                    </div>
                </div>

                <div class="campaign-hero-cta">
                    <span class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                        Shop {{ $heroCollection->localized_name }}
                    </span>
                </div>
            </a>
        </section>
    @endif

    @if($lineProducts->isNotEmpty())
        <section class="bg-white border-b border-brand-gray-border" aria-label="Shop by shirt line">
            <div class="px-6 md:px-12 py-8 border-t border-brand-gray-border flex items-end justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-3">Shop by colour palette</p>
                    <h2 class="font-serif uppercase leading-none text-3xl md:text-5xl">เลือกตามชุดสี</h2>
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
        <div class="mb-10 md:mb-14 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Build Your Shirt</p>
            <h2 class="font-serif uppercase leading-none text-brand-black" style="font-size: clamp(2.4rem, 6vw, 4.5rem);">
                {{ $locale === 'en' ? 'A Shirt That Is You' : 'เชิ้ตที่เป็นคุณ' }}
            </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 border-t border-l border-brand-gray-border">
            <a href="{{ route('shop.index') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">Design Your Own Style</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'Choose your color, collar, front placket, cuff, and pocket for a shirt built entirely your way.' : 'เลือกสี ปกคอ สาบหน้า ปลายแขน และกระเป๋า ออกแบบเชิ้ตในแบบของคุณเอง' }}</p>
            </a>
            <a href="{{ route('about') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">Premium Japanese Cotton</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'A soft, breathable fabric that stays comfortable and holds its shape all day.' : 'ผ้าเนื้อนุ่ม ใส่สบาย ระบายอากาศดี และอยู่ทรงสวยตลอดวัน' }}</p>
            </a>
            <a href="{{ route('color-library') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">50+ Colors Available</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'Over fifty shades in classic, muted, and statement tones for any day.' : 'สีให้เลือกกว่า 50 เฉด ทั้งโทนคลาสสิก สุภาพ และสีชัดสำหรับวันพิเศษ' }}</p>
                @if($heroColors->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-1.5">
                        @foreach($heroColors as $color)
                            <span class="h-5 w-5 rounded-full border border-brand-gray-border"
                                  style="background-color: {{ $color->color_code ?? '#eeeeee' }}"
                                  role="img"
                                  aria-label="{{ $color->localized_name }}"
                                  title="{{ $color->localized_name }}"></span>
                        @endforeach
                    </div>
                @endif
            </a>
            <a href="{{ route('pages.size-guide') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">S to XL Sizing</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'Sizes S to XL in an easy straight cut. Compare with the size chart to find your fit.' : 'ไซส์ S ถึง XL ทรงตรงใส่ง่าย เทียบสัดส่วนจากตารางไซส์เพื่อหาตัวที่พอดี' }}</p>
            </a>
            <a href="{{ route('shop.index') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">Designed For Everyone</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'A unisex design made for every gender, age, and style.' : 'ดีไซน์ unisex เหมาะกับทุกเพศ ทุกวัย และทุกสไตล์การแต่งตัว' }}</p>
            </a>
            <a href="{{ route('pages.contact') }}" class="p-7 md:p-8 border-r border-b border-brand-gray-border group">
                <h3 class="font-serif text-lg uppercase tracking-[0.04em]">Made For You</h3>
                <p class="mt-3 text-sm text-brand-gray-medium leading-relaxed">{{ $locale === 'en' ? 'Have a specific design in mind? Send us the details and our team will craft a shirt made just for you.' : 'มีดีไซน์เฉพาะที่อยากได้? ส่งรายละเอียดมาให้ทีมงานพิจารณา แล้วเราจะสร้างเชิ้ตที่ออกแบบมาเพื่อคุณโดยเฉพาะ' }}</p>
                <span class="mt-4 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">{{ $locale === 'en' ? 'Contact us' : 'ติดต่อเรา' }}</span>
            </a>
        </div>
    </section>

    <section class="px-6 md:px-12 py-16 md:py-24 bg-white border-b border-brand-gray-border" aria-label="Who it is for">
        <div class="mb-10 md:mb-14 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Who It Is For</p>
            <h2 class="font-serif uppercase leading-none text-brand-black text-3xl md:text-5xl">
                {{ $locale === 'en' ? 'Who It Is For' : 'เสื้อเชิ้ตเหมาะกับใคร' }}
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 border-t border-l border-brand-gray-border">
            <div class="p-7 md:p-9 border-r border-b border-brand-gray-border flex items-start gap-5">
                <span class="font-serif text-2xl text-brand-gray-light leading-none">01</span>
                <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Professionals who want a polished, credible, put-together look.' : 'คนทำงานที่อยากได้ลุคสุภาพ ดูดี และน่าเชื่อถือ' }}</p>
            </div>
            <div class="p-7 md:p-9 border-r border-b border-brand-gray-border flex items-start gap-5">
                <span class="font-serif text-2xl text-brand-gray-light leading-none">02</span>
                <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Students and young people who like simple dressing with real character.' : 'นักศึกษาและวัยรุ่นที่ชอบแต่งตัวเรียบง่ายแต่มีสไตล์' }}</p>
            </div>
            <div class="p-7 md:p-9 border-r border-b border-brand-gray-border flex items-start gap-5">
                <span class="font-serif text-2xl text-brand-gray-light leading-none">03</span>
                <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Anyone after a good-quality shirt comfortable enough to wear every day.' : 'คนที่มองหาเสื้อคุณภาพดี ใส่สบายได้ทุกวัน' }}</p>
            </div>
            <div class="p-7 md:p-9 border-r border-b border-brand-gray-border flex items-start gap-5">
                <span class="font-serif text-2xl text-brand-gray-light leading-none">04</span>
                <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'People who want one shirt for many occasions: work, travel, cafes, or events.' : 'คนที่อยากได้เชิ้ตตัวเดียว ใส่ได้หลายโอกาส ทั้งทำงาน เที่ยว คาเฟ่ หรือออกงาน' }}</p>
            </div>
            <div class="p-7 md:p-9 border-r border-b border-brand-gray-border flex items-start gap-5 md:col-span-2">
                <span class="font-serif text-2xl text-brand-gray-light leading-none">05</span>
                <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Those who want a shirt that reflects who they are and their own style.' : 'คนที่อยากมีเชิ้ตที่สะท้อนตัวตนและสไตล์ของตัวเอง' }}</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-12 border-t border-b border-brand-gray-border bg-white" aria-label="CHOMIN editorial">
        <div class="order-2 lg:order-1 lg:col-span-5 px-6 md:px-12 py-14 md:py-20 flex items-center">
            <div class="max-w-xl">
                <h2 class="font-serif uppercase leading-none text-4xl md:text-6xl">Simple. Comfortable. Your Style.</h2>
                <div class="mt-8 space-y-4 text-sm md:text-base text-brand-gray-dark leading-relaxed">
                    <p>สไตล์ที่ดี เริ่มจากความเรียบง่าย CHO.MIN ทำเชิ้ตจากผ้า Premium Japanese Cotton ที่ใส่สบายและระบายอากาศดี ให้เลือกได้ทั้งสี คอเสื้อ สาบหน้า ปลายแขน และกระเป๋า ตามสไตล์ของคุณ</p>
                    <p>ดีไซน์ unisex ใส่ได้ทุกเพศทุกวัย ไซส์ S&ndash;XL ใส่สบายตั้งแต่วันทำงานจนถึงวันสบาย ๆ</p>
                    <p>คุณภาพระดับพรีเมียม ในราคาที่เข้าถึงได้ เริ่มต้นเพียง ฿1,190</p>
                </div>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Shop all</a>
                    <a href="{{ route('stories.index') }}" class="text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">Stories</a>
                </div>
            </div>
        </div>
        <div class="order-1 lg:order-2 lg:col-span-7 border-b lg:border-b-0 lg:border-l border-brand-gray-border">
            <img src="{{ $campaignImages['brandHero'] }}"
                 alt="CHO.MIN design your own shirt — fabrics, collars and details"
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
