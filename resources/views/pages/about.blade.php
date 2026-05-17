<x-layouts.shop :title="$title" :description="$description" :ogImage="$ogImage">

    @php
        $lifestyle = \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/lifestyle-editorial.jpg');
        $customDetails = \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/custom-details.jpg');
        $duoBox = \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/duo-box.jpg');
        $careStudio = \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/care-studio.jpg');
        $locale = app()->getLocale();
    @endphp

    {{-- ── HERO ──────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden bg-white border-b border-brand-gray-border">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="px-6 md:px-14 py-20 md:py-28 flex flex-col justify-end">
                <p class="text-xs uppercase tracking-[0.22em] text-brand-gray-light mb-6">
                    {{ $locale === 'en' ? 'The Brand' : 'เกี่ยวกับ CHO.MIN' }}
                </p>
                <h1 class="font-serif uppercase leading-none text-brand-black" style="font-size: clamp(3rem, 7vw, 7rem);">
                    {!! $locale === 'en' ? 'Shirt, Your Way.' : 'เชิ้ต<br>ในแบบคุณ' !!}
                </h1>
                <div class="mt-10 max-w-sm space-y-4 text-sm text-brand-gray-dark leading-relaxed">
                    @if($aboutContent)
                        <div class="prose prose-sm max-w-none">{!! $aboutContent !!}</div>
                    @else
                        <p>{{ $locale === 'en'
                            ? 'CHO.MIN started with one idea: a great shirt should fit you — not the other way around.'
                            : 'CHO.MIN เริ่มจากความเชื่อว่า เชิ้ตที่ดีต้องตอบชีวิตของคุณ ไม่ใช่คุณที่ต้องปรับตัวให้เข้ากับเชิ้ต' }}
                        </p>
                        <p>{{ $locale === 'en'
                            ? 'We built a system that lets you choose the color, the collar, the cuff, the pocket — and wear a shirt that truly belongs to you.'
                            : 'เราจึงสร้างระบบที่ให้คุณเลือกสี คอเสื้อ ปลายแขน และกระเป๋าได้เอง เพื่อให้ทุกตัวที่ใส่รู้สึกว่า "นี่คือเชิ้ตของฉัน" จริงๆ' }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="lg:border-l border-t lg:border-t-0 border-brand-gray-border">
                <img src="{{ $lifestyle }}"
                     alt="{{ $locale === 'en' ? 'CHO.MIN lifestyle editorial' : 'CHO.MIN เชิ้ตสำหรับทุกวัน' }}"
                     class="h-full min-h-[480px] w-full object-cover"
                     fetchpriority="high">
            </div>
        </div>
    </section>

    {{-- ── MANIFESTO ────────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-brand-gray-border px-6 md:px-14 py-16 md:py-24">
        <div class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.22em] text-brand-gray-light mb-8">
                {{ $locale === 'en' ? 'What We Believe' : 'สิ่งที่เราเชื่อ' }}
            </p>
            <blockquote class="font-serif uppercase leading-tight text-brand-black" style="font-size: clamp(1.6rem, 3.5vw, 3rem);">
                {{ $locale === 'en'
                    ? '"Style is not a size. It\'s not a standard. It\'s the detail you choose every morning."'
                    : '"สไตล์ไม่ใช่ไซส์ ไม่ใช่มาตรฐาน มันคือรายละเอียดที่คุณเลือกทุกเช้า"' }}
            </blockquote>
        </div>
    </section>

    {{-- ── HOW IT WORKS ─────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-brand-gray-border" aria-label="How CHO.MIN works">
        <div class="px-6 md:px-14 py-12 border-b border-brand-gray-border">
            <p class="text-xs uppercase tracking-[0.22em] text-brand-gray-light mb-3">
                {{ $locale === 'en' ? 'How It Works' : 'วิธีการทำงาน' }}
            </p>
            <h2 class="font-serif uppercase leading-none text-3xl md:text-5xl">
                {{ $locale === 'en' ? 'Three Steps to Your Shirt' : 'สามขั้นตอน สู่เชิ้ตของคุณ' }}
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-brand-gray-border">
            <div class="p-8 md:p-10">
                <span class="block font-serif text-5xl leading-none text-brand-black mb-6">01</span>
                <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">
                    {{ $locale === 'en' ? 'Choose Your Color' : 'เลือกสี' }}
                </h3>
                <p class="text-sm text-brand-gray-dark leading-relaxed">
                    {{ $locale === 'en'
                        ? '50+ colors — from muted tonals to saturated statements. Every shade is carefully selected for wearability.'
                        : 'กว่า 50 สี ตั้งแต่โทนมินิมอลสำหรับทุกวัน ไปจนถึงเฉดจัดจ้านสำหรับวันพิเศษ ทุกสีคัดมาเพื่อใส่ได้จริง' }}
                </p>
            </div>
            <div class="p-8 md:p-10">
                <span class="block font-serif text-5xl leading-none text-brand-black mb-6">02</span>
                <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">
                    {{ $locale === 'en' ? 'Pick Your Details' : 'เลือกรายละเอียด' }}
                </h3>
                <p class="text-sm text-brand-gray-dark leading-relaxed">
                    {{ $locale === 'en'
                        ? 'Collar style, cuff shape, pocket or no pocket. Small choices that make a shirt feel entirely yours.'
                        : 'คอเสื้อ ปลายแขน และกระเป๋า ตัดสินใจ 3 อย่าง แค่นั้นก็ได้เชิ้ตที่ไม่เหมือนใคร' }}
                </p>
            </div>
            <div class="p-8 md:p-10">
                <span class="block font-serif text-5xl leading-none text-brand-black mb-6">03</span>
                <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">
                    {{ $locale === 'en' ? 'Find Your Size' : 'เลือกไซส์' }}
                </h3>
                <p class="text-sm text-brand-gray-dark leading-relaxed">
                    {{ $locale === 'en'
                        ? 'XS to 6XL. A unisex cut designed to drape well across all body types — slim, regular, and plus.'
                        : 'XS ถึง 6XL ทรง Unisex ที่ออกแบบให้ตกทรงดีในทุกสัดส่วน ทั้งเล็ก กลาง และพลัสไซส์' }}
                </p>
            </div>
        </div>
    </section>

    {{-- ── EDITORIAL IMAGES ─────────────────────────────────────────── --}}
    <section class="grid grid-cols-1 md:grid-cols-3 border-b border-brand-gray-border bg-white" aria-label="CHO.MIN shirt craftsmanship">
        <div class="relative overflow-hidden border-b md:border-b-0 md:border-r border-brand-gray-border">
            <img src="{{ $customDetails }}"
                 alt="{{ $locale === 'en' ? 'CHO.MIN collar and cuff detail' : 'รายละเอียดคอเสื้อและปลายแขน CHO.MIN' }}"
                 class="h-72 md:h-96 w-full object-cover"
                 loading="lazy">
            <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/40 to-transparent">
                <p class="text-xs uppercase tracking-[0.16em] text-white/80">{{ $locale === 'en' ? 'Custom Details' : 'รายละเอียดที่เลือกได้' }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden border-b md:border-b-0 md:border-r border-brand-gray-border">
            <img src="{{ $duoBox }}"
                 alt="{{ $locale === 'en' ? 'CHO.MIN shirts gift packaging' : 'แพ็กเกจเชิ้ต CHO.MIN' }}"
                 class="h-72 md:h-96 w-full object-cover"
                 loading="lazy">
            <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/40 to-transparent">
                <p class="text-xs uppercase tracking-[0.16em] text-white/80">{{ $locale === 'en' ? 'Ready to Gift' : 'พร้อมเป็นของขวัญ' }}</p>
            </div>
        </div>
        <div class="relative overflow-hidden">
            <img src="{{ $careStudio }}"
                 alt="{{ $locale === 'en' ? 'CHO.MIN shirt care' : 'การดูแลเชิ้ต CHO.MIN' }}"
                 class="h-72 md:h-96 w-full object-cover"
                 loading="lazy">
            <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/40 to-transparent">
                <p class="text-xs uppercase tracking-[0.16em] text-white/80">{{ $locale === 'en' ? 'Premium Fabric' : 'ผ้าคุณภาพ' }}</p>
            </div>
        </div>
    </section>

    {{-- ── BRAND VALUES ─────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-brand-gray-border px-6 md:px-14 py-16 md:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-brand-gray-light mb-8">
                    {{ $locale === 'en' ? 'Our Commitment' : 'สิ่งที่เรายึดมั่น' }}
                </p>
                <div class="space-y-8">
                    <div class="border-t border-brand-gray-border pt-6">
                        <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">{{ $locale === 'en' ? 'Quality Without Compromise' : 'คุณภาพที่ไม่ลดทอน' }}</h3>
                        <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Every shirt is made from fabric we\'d want to wear ourselves — soft, breathable, and structured to hold its shape wash after wash.' : 'ทุกตัวผลิตจากผ้าที่เราเลือกเองว่าอยากใส่ นุ่ม ระบาย และทรงดีแม้หลังซักซ้ำ' }}</p>
                    </div>
                    <div class="border-t border-brand-gray-border pt-6">
                        <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">{{ $locale === 'en' ? 'Inclusive Sizing' : 'ไซส์สำหรับทุกคน' }}</h3>
                        <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'XS to 6XL, unisex. We believe great design should fit everyone — not just a specific body type.' : 'XS ถึง 6XL ทรง Unisex เพราะดีไซน์ที่ดีต้องใส่ได้ทุกคน ไม่ใช่แค่บางสัดส่วน' }}</p>
                    </div>
                    <div class="border-t border-brand-gray-border pt-6">
                        <h3 class="text-xs uppercase tracking-[0.16em] text-brand-black mb-3">{{ $locale === 'en' ? 'Free Exchange, 30 Days' : 'เปลี่ยนได้ฟรี 30 วัน' }}</h3>
                        <p class="text-sm text-brand-gray-dark leading-relaxed">{{ $locale === 'en' ? 'Unsure about size? Exchange within 30 days, no questions asked. We want you to love what you wear.' : 'ไม่แน่ใจเรื่องไซส์? เปลี่ยนได้ฟรีภายใน 30 วัน เราต้องการให้คุณรู้สึกดีกับสิ่งที่ใส่' }}</p>
                    </div>
                </div>
            </div>
            <div class="lg:border-l border-t lg:border-t-0 border-brand-gray-border lg:pl-16 pt-8 lg:pt-0">
                <p class="text-xs uppercase tracking-[0.22em] text-brand-gray-light mb-8">
                    {{ $locale === 'en' ? 'By the Numbers' : 'ตัวเลขที่เล่าเรื่อง' }}
                </p>
                <div class="space-y-8">
                    <div class="border-t border-brand-gray-border pt-6">
                        <span class="block font-serif text-5xl leading-none text-brand-black">50+</span>
                        <p class="mt-2 text-xs uppercase tracking-[0.14em] text-brand-gray-medium">{{ $locale === 'en' ? 'Colors to choose' : 'สีให้เลือก' }}</p>
                    </div>
                    <div class="border-t border-brand-gray-border pt-6">
                        <span class="block font-serif text-5xl leading-none text-brand-black">XS–6XL</span>
                        <p class="mt-2 text-xs uppercase tracking-[0.14em] text-brand-gray-medium">{{ $locale === 'en' ? 'Size range, unisex' : 'ไซส์ครอบคลุม ทรง Unisex' }}</p>
                    </div>
                    <div class="border-t border-brand-gray-border pt-6">
                        <span class="block font-serif text-5xl leading-none text-brand-black">3</span>
                        <p class="mt-2 text-xs uppercase tracking-[0.14em] text-brand-gray-medium">{{ $locale === 'en' ? 'Details to customize' : 'รายละเอียดที่ปรับได้' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA ──────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden bg-brand-black text-white px-6 md:px-14 py-20 md:py-28">
        <img src="{{ $lifestyle }}"
             alt=""
             class="absolute inset-0 h-full w-full object-cover opacity-25"
             loading="lazy">
        <div class="absolute inset-0 bg-brand-black/60"></div>
        <div class="relative z-10 max-w-xl">
            <p class="text-xs uppercase tracking-[0.22em] text-white/50 mb-6">CHO.MIN</p>
            <h2 class="font-serif uppercase leading-none text-white" style="font-size: clamp(2.2rem, 5vw, 5rem);">
                {{ $locale === 'en' ? 'Find Your Shirt.' : 'ค้นพบเชิ้ตของคุณ' }}
            </h2>
            <div class="mt-10 flex flex-wrap gap-6">
                <a href="{{ route('shop.index') }}"
                   class="text-xs uppercase tracking-[0.18em] border-b border-white pb-1 hover:text-white/70 transition-colors">
                    {{ $locale === 'en' ? 'Shop All' : 'ชมสินค้าทั้งหมด' }}
                </a>
                <a href="{{ route('color-library') }}"
                   class="text-xs uppercase tracking-[0.18em] border-b border-white/40 pb-1 text-white/70 hover:text-white hover:border-white transition-colors">
                    {{ $locale === 'en' ? 'Color Library' : 'คลังสี 50+ เฉด' }}
                </a>
            </div>
        </div>
    </section>

</x-layouts.shop>
