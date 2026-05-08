<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Search' : 'ค้นหา').' | CHOMIN'">
    <section class="px-6 md:px-12 py-12 md:py-16 border-b border-brand-gray-border bg-white">
        <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Search</p>
        <h1 class="font-serif uppercase leading-none text-5xl md:text-7xl">
            {{ app()->getLocale() === 'en' ? 'Find your shirt' : 'ค้นหาเชิ้ตของคุณ' }}
        </h1>
        <form method="GET" action="{{ route('search') }}" class="mt-8 max-w-3xl">
            <input name="q"
                   value="{{ $q }}"
                   placeholder="{{ app()->getLocale() === 'en' ? 'Search by color, collar, collection' : 'ค้นหาด้วยสี คอเสื้อ หรือคอลเล็คชัน' }}"
                   class="w-full border-0 border-b border-brand-black px-0 py-4 text-xl md:text-2xl focus:ring-0">
        </form>
    </section>

    @if($q)
        <div class="px-6 md:px-12 py-4 border-b border-brand-gray-border text-xs uppercase tracking-[0.14em] text-brand-gray-light">
            {{ $products->total() }} results for "{{ $q }}"
        </div>
    @endif

    <section class="bg-white">
        <div class="commerce-grid">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full px-6 py-24 text-center">
                    <p class="text-sm text-brand-gray-medium">
                        {{ $q ? 'ไม่พบสินค้าที่ตรงกับคำค้นหา' : 'พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา' }}
                    </p>
                    <a href="{{ route('shop.index') }}" class="mt-5 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                        Shop all
                    </a>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="px-6 md:px-12 py-10 border-t border-brand-gray-border">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</x-layouts.shop>
