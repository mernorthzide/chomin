@props(['excludeId' => null, 'limit' => 6])

@php
    $ids = collect(session('recently_viewed_products', []))
        ->reject(fn ($id) => $excludeId && $id == $excludeId)
        ->take($limit)
        ->values()
        ->all();

    $recentlyViewed = collect();
    if (! empty($ids)) {
        $recentlyViewed = \App\Models\Product::active()
            ->whereIn('id', $ids)
            ->with(['primaryImage', 'images', 'variants', 'translations', 'collection.translations', 'colors.translations'])
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();
    }
@endphp

@if($recentlyViewed->isNotEmpty())
    <section class="border-t border-brand-gray-border bg-white py-12 md:py-16">
        <div class="px-6 md:px-12">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="font-serif text-2xl uppercase md:text-3xl">
                    {{ app()->getLocale() === 'en' ? 'Recently Viewed' : 'ดูล่าสุด' }}
                </h2>
                <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">
                    {{ $recentlyViewed->count() }} {{ app()->getLocale() === 'en' ? 'items' : 'รายการ' }}
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                @foreach($recentlyViewed as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endif
