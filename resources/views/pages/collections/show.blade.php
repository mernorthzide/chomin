<x-layouts.shop :title="$title" :description="$description" :ogImage="$ogImage">

    @php
        $collectionImage = $collection->banner_image
            ? \Illuminate\Support\Facades\Storage::url($collection->banner_image)
            : ($collection->image ? \Illuminate\Support\Facades\Storage::url($collection->image) : null);
    @endphp

    <section class="relative min-h-[72svh] overflow-hidden border-b border-brand-gray-border bg-white">
        @if($collectionImage)
            <img src="{{ $collectionImage }}"
                 alt="{{ $collection->localized_name }}"
                 class="absolute inset-0 h-full w-full object-cover object-center">
        @endif
        <div class="absolute inset-0 bg-white/10"></div>
        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-white/85 via-white/50 to-transparent" aria-hidden="true"></div>
        <div class="relative z-10 flex min-h-[72svh] flex-col justify-end px-6 md:px-12 py-10 md:py-14">
            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-dark mb-4">{{ app()->getLocale() === 'en' ? 'Collection' : 'คอลเล็คชัน' }}</p>
            <h1 class="font-serif uppercase leading-none text-brand-black max-w-5xl" style="font-size: clamp(3rem, 10vw, 9rem);">
                {{ $collection->localized_name }}
            </h1>
            @if($collection->localized_description)
                <p class="mt-5 max-w-xl text-sm md:text-base text-brand-gray-dark leading-relaxed">
                    {{ $collection->localized_description }}
                </p>
            @endif
        </div>
    </section>

    <section class="shop-filter-bar sticky z-30 border-b border-brand-gray-border bg-white" style="top: var(--nav-height);">
        <div class="px-6 md:px-12 py-4">
            <form method="GET" action="{{ route('collections.show', $collection->slug) }}" class="grid grid-cols-2 items-end gap-4 md:flex md:flex-wrap md:items-center md:gap-6">
                <div class="filter-field">
                    <label for="category">{{ app()->getLocale() === 'en' ? 'Category' : 'หมวดหมู่' }}</label>
                    <select id="category" name="category">
                        <option value="">{{ app()->getLocale() === 'en' ? 'All' : 'ทั้งหมด' }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                {{ $category->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="sort">{{ app()->getLocale() === 'en' ? 'Sort by' : 'เรียงตาม' }}</label>
                    <select id="sort" name="sort">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Newest' : 'ใหม่สุด' }}</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Price low–high' : 'ราคาต่ำ-สูง' }}</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Price high–low' : 'ราคาสูง-ต่ำ' }}</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>{{ app()->getLocale() === 'en' ? 'Name A–Z' : 'ชื่อ A-Z' }}</option>
                    </select>
                </div>

                <div class="filter-field col-span-2 md:col-span-1">
                    <button type="submit" class="min-h-[44px] w-full bg-brand-black px-4 text-xs uppercase tracking-[0.14em] text-white md:w-auto">
                        {{ app()->getLocale() === 'en' ? 'Apply filters' : 'ใช้ตัวกรอง' }}
                    </button>
                </div>

                <div class="col-span-2 text-xs uppercase tracking-[0.14em] text-brand-gray-light md:ml-auto md:col-span-1">
                    {{ $products->total() }} {{ app()->getLocale() === 'en' ? 'items' : 'รายการ' }}
                </div>

                @if(request()->hasAny(['category']))
                    <a href="{{ route('collections.show', $collection->slug) }}" class="text-xs uppercase tracking-[0.14em] border-b border-brand-black pb-1">
                        {{ app()->getLocale() === 'en' ? 'Clear filters' : 'ล้างตัวกรอง' }}
                    </a>
                @endif
            </form>
        </div>
    </section>

    <section class="bg-white">
        <div class="commerce-grid">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full px-6 py-24 text-center">
                    <p class="text-sm text-brand-gray-medium">{{ app()->getLocale() === 'en' ? 'No products found in this collection' : 'ไม่พบสินค้าในคอลเล็คชันนี้' }}</p>
                    <a href="{{ route('collections.show', $collection->slug) }}" class="mt-5 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                        {{ app()->getLocale() === 'en' ? 'Clear all filters' : 'ล้างตัวกรองทั้งหมด' }}
                    </a>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="px-6 md:px-12 py-10 border-t border-brand-gray-border">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </section>

</x-layouts.shop>
