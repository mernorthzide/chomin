<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Special Price' : 'สินค้าราคาพิเศษ').' | CHOMIN'">
    <section class="px-6 md:px-12 py-12 md:py-16 border-b border-brand-gray-border bg-white">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Special Price</p>
                <h1 class="font-serif uppercase leading-none text-5xl md:text-7xl">
                    {{ app()->getLocale() === 'en' ? 'Sale' : 'ราคาพิเศษ' }}
                </h1>
            </div>
            <p class="max-w-md text-sm text-brand-gray-medium leading-relaxed">
                โปรโมชันของ CHOMIN พร้อมเงื่อนไขจัดส่งและเปลี่ยนคืนเหมือนสินค้าปกติ
            </p>
        </div>
    </section>

    <section class="bg-white">
        <div class="commerce-grid">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full px-6 py-24 text-center">
                    <p class="text-sm text-brand-gray-medium">ตอนนี้ยังไม่มีสินค้าราคาพิเศษ</p>
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
