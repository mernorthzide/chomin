<x-layouts.shop :title="'404 | CHOMIN'">
    @php($errorLocale = in_array(request()->segment(1), config('chomin.locales.supported', ['th', 'en']), true) ? request()->segment(1) : app()->getLocale())
    <section class="min-h-[70vh] px-6 md:px-12 py-20 flex items-center">
        <div class="max-w-4xl mx-auto w-full">
            <p class="text-xs uppercase tracking-[0.24em] text-brand-gray-light mb-6">404</p>
            <h1 class="font-serif text-5xl md:text-7xl uppercase leading-none mb-8">
                {{ $errorLocale === 'en' ? 'Page not found' : 'ไม่พบหน้าที่ต้องการ' }}
            </h1>
            <p class="text-sm md:text-base text-brand-gray-dark leading-relaxed max-w-xl mb-10">
                {{ $errorLocale === 'en'
                    ? 'The page may have moved, or the link may no longer be available.'
                    : 'หน้านี้อาจถูกย้ายแล้ว หรือลิงก์เดิมอาจไม่พร้อมใช้งาน' }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('shop.index') }}" class="bg-brand-black text-white px-8 py-3 text-xs uppercase tracking-[0.15em]">
                    {{ $errorLocale === 'en' ? 'Shop' : 'เลือกซื้อสินค้า' }}
                </a>
                <a href="{{ route('home') }}" class="border border-brand-black px-8 py-3 text-xs uppercase tracking-[0.15em]">
                    {{ $errorLocale === 'en' ? 'Home' : 'หน้าแรก' }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.shop>
