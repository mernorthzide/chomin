<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Sale' : 'สินค้าลดราคา').' | CHOMIN'">
    <section class="px-6 md:px-12 py-16">
        <div class="max-w-6xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-12">{{ app()->getLocale() === 'en' ? 'Sale' : 'สินค้าลดราคา' }}</h1>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        </div>
    </section>
</x-layouts.shop>
