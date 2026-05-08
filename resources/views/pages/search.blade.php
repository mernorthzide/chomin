<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Search' : 'ค้นหา').' | CHOMIN'">
    <section class="px-6 md:px-12 py-12">
        <div class="max-w-6xl mx-auto">
            <h1 class="font-serif text-4xl md:text-6xl uppercase mb-8">{{ app()->getLocale() === 'en' ? 'Search' : 'ค้นหา' }}</h1>
            <form method="GET" action="{{ route('search') }}" class="mb-10">
                <input name="q" value="{{ $q }}" placeholder="{{ app()->getLocale() === 'en' ? 'Search products' : 'ค้นหาสินค้า' }}" class="w-full border-brand-black text-lg py-4">
            </form>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        </div>
    </section>
</x-layouts.shop>
