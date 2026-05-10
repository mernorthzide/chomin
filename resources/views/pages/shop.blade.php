<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Shop' : 'ร้านค้า').' | CHOMIN'">

    @php
        $shopHeroImage = \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/lifestyle-editorial.jpg');
    @endphp

    <section class="page-kicker border-b border-brand-gray-border bg-white">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="lg:col-span-7 px-6 md:px-12 py-12 md:py-16 flex items-end">
                <div class="w-full">
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Products</p>
                    <h1 class="font-serif uppercase leading-none text-5xl md:text-7xl">
                        {{ app()->getLocale() === 'en' ? 'Design Your Own Shirt' : 'Design Your Own Shirt' }}
                    </h1>
                    <p class="mt-6 max-w-md text-sm text-brand-gray-medium leading-relaxed">
                        เลือกจาก 5 shirt lines ที่ต่อยอดจาก Facebook campaign: classic, workday, pastel, statement color และ mandarin minimal ราคาโปร 999 บาท พร้อมปรับคอเสื้อ ปลายแขน และกระเป๋าได้
                    </p>
                </div>
            </div>
            <div class="lg:col-span-5 border-t lg:border-t-0 lg:border-l border-brand-gray-border">
                <img src="{{ $shopHeroImage }}"
                     alt="CHO.MIN custom shirt lifestyle"
                     class="h-full min-h-[280px] w-full object-cover"
                     loading="eager">
            </div>
        </div>
    </section>

    <section class="shop-filter-bar sticky z-30 border-b border-brand-gray-border bg-white" style="top: 60px;">
        <div class="px-6 md:px-12 py-4">
            <form method="GET" action="{{ route('shop.index') }}" class="grid grid-cols-2 items-end gap-4 md:flex md:flex-wrap md:items-center md:gap-6">
                <div class="filter-field">
                    <label for="category">หมวดหมู่</label>
                    <select id="category" name="category" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                {{ $category->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="collection">คอลเล็คชัน</label>
                    <select id="collection" name="collection" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection->slug }}" {{ request('collection') === $collection->slug ? 'selected' : '' }}>
                                {{ $collection->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="size">ไซส์</label>
                    <select id="size" name="size" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        @foreach($availableSizes as $size)
                            <option value="{{ $size }}" {{ request('size') === $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label for="sort">เรียงตาม</label>
                    <select id="sort" name="sort" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>แนะนำ</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>ราคาต่ำ-สูง</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>ราคาสูง-ต่ำ</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>ชื่อ A-Z</option>
                    </select>
                </div>

                <div class="col-span-2 text-xs uppercase tracking-[0.14em] text-brand-gray-light md:ml-auto md:col-span-1">
                    {{ $products->total() }} รายการ
                </div>

                @if(request()->hasAny(['category', 'collection', 'color', 'size']))
                    <a href="{{ route('shop.index') }}" class="text-xs uppercase tracking-[0.14em] border-b border-brand-black pb-1">
                        ล้างตัวกรอง
                    </a>
                @endif
            </form>

            @if($availableColors->isNotEmpty())
                <div class="shop-color-scroll mt-4 flex items-center gap-2 overflow-x-auto pb-2">
                    <span class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light mr-1">Color</span>
                    @foreach($availableColors as $color)
                        @php
                            $colorKey = $color->filter_key;
                            $colorQuery = array_filter(array_merge(request()->except(['page', 'color']), ['color' => $colorKey]), fn ($value) => filled($value));
                        @endphp
                        <a href="{{ route('shop.index', $colorQuery) }}"
                           class="h-9 w-9 flex-shrink-0 rounded-full border {{ request('color') === $colorKey ? 'border-brand-black ring-2 ring-brand-black ring-offset-2' : 'border-brand-gray-border' }}"
                           style="background-color: {{ $color->color_code ?? '#eeeeee' }}"
                           title="{{ $color->localized_name }}"
                           aria-label="{{ $color->localized_name }}"></a>
                    @endforeach
                    @if(request('color'))
                        <a href="{{ route('shop.index', request()->except(['page', 'color'])) }}" class="ml-2 text-[11px] uppercase tracking-[0.14em] text-brand-gray-medium underline">
                            clear color
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white">
        <div class="commerce-grid">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full px-6 py-24 text-center">
                    <p class="text-sm text-brand-gray-medium">ไม่พบสินค้า</p>
                    <a href="{{ route('shop.index') }}" class="mt-5 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                        ล้างตัวกรองทั้งหมด
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
