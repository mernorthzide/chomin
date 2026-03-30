<x-layouts.shop>

    {{-- ============================================================
         COLLECTION BANNER
    ============================================================ --}}
    <section class="relative w-full aspect-[16/6] md:aspect-[21/7] overflow-hidden bg-brand-black">
        @if($collection->banner_image)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($collection->banner_image) }}"
                alt="{{ $collection->name }}"
                class="absolute inset-0 w-full h-full object-cover opacity-70">
        @elseif($collection->image)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($collection->image) }}"
                alt="{{ $collection->name }}"
                class="absolute inset-0 w-full h-full object-cover opacity-70">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/50"></div>
        <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-6">
            <p class="text-xs text-white/60 tracking-[0.25em] uppercase mb-3">คอลเล็คชัน</p>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-normal text-white uppercase tracking-widest">
                {{ $collection->name }}
            </h1>
            @if($collection->description)
                <p class="mt-4 text-sm text-white/70 max-w-xl tracking-wide leading-relaxed">
                    {{ $collection->description }}
                </p>
            @endif
        </div>
    </section>

    {{-- ============================================================
         FILTER BAR
    ============================================================ --}}
    <section class="bg-white sticky top-16 z-40 border-b border-brand-gray-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" action="{{ route('collections.show', $collection->slug) }}"
                  class="flex flex-wrap items-center gap-3">

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
                @if(request()->hasAny(['category']))
                    <a href="{{ route('collections.show', $collection->slug) }}"
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
                    <p class="text-sm text-brand-gray-medium">ไม่พบสินค้าในคอลเล็คชันนี้</p>
                    @if(request()->hasAny(['category']))
                        <a href="{{ route('collections.show', $collection->slug) }}"
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
