<x-layouts.shop>

    <!-- Page Header -->
    <section class="bg-brand-black py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-serif text-4xl md:text-5xl font-normal text-white uppercase tracking-widest">
                ร้านค้า
            </h1>
            <p class="mt-4 text-sm text-white/60 tracking-wide">
                สินค้าทั้งหมดจาก CHOMIN
            </p>
        </div>
    </section>

    {{-- ============================================================
         FILTER BAR
    ============================================================ --}}
    <section class="bg-white sticky top-16 z-40 border-b border-brand-gray-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" action="{{ route('shop.index') }}"
                  class="flex flex-wrap items-center gap-3 md:gap-5">

                <!-- Category Filter -->
                <div class="flex items-center space-x-2">
                    <label for="category" class="text-xs font-medium tracking-wider text-brand-gray-medium uppercase whitespace-nowrap">หมวดหมู่</label>
                    <select id="category" name="category"
                            onchange="this.form.submit()"
                            class="text-sm border-0 border-b border-brand-gray-border bg-transparent py-1 pr-6 pl-0 text-brand-black focus:ring-0 focus:border-brand-black cursor-pointer">
                        <option value="">ทั้งหมด</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}"
                                    {{ request('category') === $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-px h-4 bg-brand-gray-border hidden sm:block"></div>

                <!-- Collection Filter -->
                <div class="flex items-center space-x-2">
                    <label for="collection" class="text-xs font-medium tracking-wider text-brand-gray-medium uppercase whitespace-nowrap">คอลเล็คชัน</label>
                    <select id="collection" name="collection"
                            onchange="this.form.submit()"
                            class="text-sm border-0 border-b border-brand-gray-border bg-transparent py-1 pr-6 pl-0 text-brand-black focus:ring-0 focus:border-brand-black cursor-pointer">
                        <option value="">ทั้งหมด</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection->slug }}"
                                    {{ request('collection') === $collection->slug ? 'selected' : '' }}>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-px h-4 bg-brand-gray-border hidden sm:block"></div>

                <!-- Sort -->
                <div class="flex items-center space-x-2">
                    <label for="sort" class="text-xs font-medium tracking-wider text-brand-gray-medium uppercase whitespace-nowrap">เรียงตาม</label>
                    <select id="sort" name="sort"
                            onchange="this.form.submit()"
                            class="text-sm border-0 border-b border-brand-gray-border bg-transparent py-1 pr-6 pl-0 text-brand-black focus:ring-0 focus:border-brand-black cursor-pointer">
                        <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>ใหม่สุด</option>
                        <option value="price_asc"  {{ $sort === 'price_asc'  ? 'selected' : '' }}>ราคาต่ำ–สูง</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>ราคาสูง–ต่ำ</option>
                        <option value="name_asc"   {{ $sort === 'name_asc'   ? 'selected' : '' }}>ชื่อ A–Z</option>
                    </select>
                </div>

                <!-- Result Count -->
                <div class="ml-auto text-xs text-brand-gray-light tracking-wide">
                    {{ $products->total() }} รายการ
                </div>

                <!-- Clear Filters -->
                @if(request()->hasAny(['category', 'collection']))
                    <a href="{{ route('shop.index') }}"
                       class="text-xs text-brand-brown hover:underline tracking-wide">
                        ล้างตัวกรอง
                    </a>
                @endif
            </form>
        </div>
    </section>

    {{-- ============================================================
         PRODUCTS GRID
    ============================================================ --}}
    <section class="bg-white py-10 md:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse($products as $product)
                @if($loop->first)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                @endif

                <div>
                    <x-product-card :product="$product" />
                </div>

                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-brand-gray-border mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm text-brand-gray-medium">ไม่พบสินค้า</p>
                    @if(request()->hasAny(['category', 'collection']))
                        <a href="{{ route('shop.index') }}"
                           class="mt-4 inline-block text-sm text-brand-brown hover:underline">
                            ล้างตัวกรองทั้งหมด
                        </a>
                    @endif
                </div>
            @endforelse

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $products->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </section>

</x-layouts.shop>
