<x-layouts.shop :title="$title" :description="$description" :ogImage="$ogImage" :jsonLd="$jsonLd ?? []" :ogType="$ogType ?? 'product'">

    <div
        x-data="productPage()"
        x-init="init()"
        class="bg-white">

        @php
            $sizeOrder = collect(['XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'])
                ->flip();
            $visibleColorLimit = 16;
            $customOptionGroups = config('chomin.custom_options');
            $customOptionDefaults = config("chomin.product_option_defaults.{$product->slug}", []);
            $productEditorialImages = [
                'customDetails' => \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/custom-details.jpg'),
                'duoBox' => \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/duo-box.jpg'),
                'careStudio' => \Illuminate\Support\Facades\Storage::url('products/chomin-imagen/care-studio.jpg'),
            ];
        @endphp

        {{-- ============================================================
             BREADCRUMB
        ============================================================ --}}
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <ol class="flex min-w-0 items-center space-x-2 overflow-hidden text-xs text-brand-gray-medium">
                <li class="shrink-0"><a href="{{ route('home') }}" class="hover:text-brand-black transition-colors">หน้าแรก</a></li>
                <li><span class="text-brand-gray-border">/</span></li>
                @if($product->collection)
                    <li class="min-w-0 shrink"><a href="{{ route('collections.show', $product->collection->slug) }}" class="block truncate hover:text-brand-black transition-colors">{{ $product->collection->name }}</a></li>
                    <li class="shrink-0"><span class="text-brand-gray-border">/</span></li>
                @endif
                <li class="min-w-0 truncate text-brand-black">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- ============================================================
             MAIN PRODUCT SECTION
        ============================================================ --}}
        <section class="px-6 md:px-12 pb-16 md:pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">

                {{-- ---- LEFT: IMAGE GALLERY ---- --}}
                <div class="space-y-4">
                    <!-- Main Image with hover zoom + click-to-lightbox -->
                    <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray group/zoom cursor-zoom-in"
                         @mousemove="zoom($event)"
                         @mouseleave="resetZoom()"
                         @click="openLightbox()">
                        <template x-if="currentImages.length > 0">
                            <img
                                :src="currentImages[activeImageIndex]"
                                :alt="'{{ $product->name }}'"
                                class="w-full h-full object-cover transition-transform duration-150"
                                :style="zoomActive ? `transform: scale(2); transform-origin: ${zoomX}% ${zoomY}%` : ''"
                                loading="eager"
                                fetchpriority="high"
                                decoding="async"
                                x-transition:enter="transition ease-in duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100">
                        </template>
                        <template x-if="currentImages.length === 0">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-brand-gray-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </template>

                        <!-- Prev/Next Arrows (show if > 1 image) -->
                        <template x-if="currentImages.length > 1">
                            <div>
                                <button @click="prevImage()"
                                        aria-label="รูปก่อนหน้า"
                                        class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-white/85 shadow-sm transition-colors duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="nextImage()"
                                        aria-label="รูปถัดไป"
                                        class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-white/85 shadow-sm transition-colors duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Thumbnail Row -->
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <template x-for="(img, idx) in currentImages" :key="idx">
                            <button
                                @click="activeImageIndex = idx"
                                class="flex-shrink-0 w-16 h-20 overflow-hidden bg-brand-gray border-2 transition-colors duration-200"
                                :class="activeImageIndex === idx ? 'border-brand-black' : 'border-transparent hover:border-brand-gray-border'">
                                <img :src="img" class="w-full h-full object-cover" role="presentation" alt="">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ---- RIGHT: PRODUCT INFO ---- --}}
                <div class="flex flex-col">

                    <!-- Collection Tag -->
                    @if($product->collection)
                        <p class="text-xs tracking-[0.2em] uppercase text-brand-gray-medium mb-2">
                            {{ $product->collection->name }}
                        </p>
                    @endif

                    <!-- Product Name -->
                    <h1 class="font-serif text-4xl md:text-6xl uppercase text-brand-black leading-none">
                        {{ $product->localized_name }}
                    </h1>

                    <!-- Price -->
                    <div class="mt-3 flex items-baseline gap-3">
                        <p class="text-xl font-normal text-brand-black">
                            ฿{{ number_format($product->display_price, 0) }}
                        </p>
                        @if($product->is_on_sale)
                            <p class="text-sm text-brand-gray-light line-through">
                                ฿{{ number_format((float) $product->price, 0) }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 border border-brand-gray-border">
                        <div class="p-4 border-b sm:border-b-0 sm:border-r border-brand-gray-border">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-brand-gray-light">Special Price</p>
                            <p class="mt-2 text-sm text-brand-black">จาก 1,790 เหลือ 999 บาท</p>
                        </div>
                        <div class="p-4">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-brand-gray-light">DuoDeal</p>
                            <p class="mt-2 text-sm text-brand-black">2 ตัว 1,850 บาท ผ่าน LINE</p>
                        </div>
                    </div>
                    <a href="https://line.me/R/ti/p/@chomin.th"
                       target="_blank"
                       rel="noopener"
                       class="mt-3 inline-flex min-h-11 items-center justify-center border border-brand-black px-5 text-xs uppercase tracking-[0.16em] hover:bg-brand-black hover:text-white">
                        Chat LINE @chomin.th
                    </a>

                    <!-- Divider -->
                    <div class="w-12 h-px bg-brand-gray-border my-6"></div>

                    @if($product->colors->isNotEmpty())
                        <!-- COLOR SELECTION -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">สี</span>
                                <span class="text-xs text-brand-gray-medium" x-text="selectedColorName"></span>
                            </div>
                            <div class="grid grid-cols-8 gap-2.5 sm:grid-cols-10">
                                @foreach($product->colors as $color)
                                    <button
                                        x-show="showAllColors || {{ $loop->iteration }} <= {{ $visibleColorLimit }}"
                                        @click="selectColor({{ $color->id }}, '{{ $color->localized_name }}', {{ json_encode($color->images->pluck('image_path')->map(fn($p) => \Illuminate\Support\Facades\Storage::url($p))->values()) }})"
                                        class="aspect-square min-h-11 border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                                        :class="{{ $color->id }} === selectedColorId ? 'border-brand-black scale-105' : 'border-brand-gray-border hover:border-brand-gray-dark'"
                                        style="background-color: {{ $color->color_code ?? '#cccccc' }}"
                                        aria-label="เลือกสี {{ $color->localized_name }}"
                                        :title="'{{ $color->localized_name }}'">
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-4">
                                @if($product->colors->count() > $visibleColorLimit)
                                    <button type="button" @click="showAllColors = !showAllColors" class="inline-flex min-h-[44px] items-center text-xs uppercase tracking-[0.14em] text-brand-gray-medium underline">
                                        <span x-text="showAllColors ? 'แสดงสีน้อยลง' : 'ดูสีทั้งหมด {{ $product->colors->count() }} สี'"></span>
                                    </button>
                                @endif
                                <a href="{{ route('color-library') }}" class="inline-block text-xs uppercase tracking-[0.14em] text-brand-gray-medium underline">
                                    ดูคลังสีทั้งหมด
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- SIZE SELECTION -->
                    @php
                        $availableSizes = $product->variants
                            ->pluck('size')
                            ->unique()
                            ->sortBy(fn ($size) => $sizeOrder->get($size, 1000))
                            ->values();
                    @endphp

                    @if($availableSizes->isNotEmpty())
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">{{ app()->getLocale() === 'en' ? 'Size' : 'ไซส์' }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-brand-gray-medium" x-text="selectedSize ? selectedSize : '{{ app()->getLocale() === 'en' ? 'Select' : 'กรุณาเลือก' }}'"></span>
                                    <a href="{{ route('pages.size-guide') }}" class="text-xs uppercase tracking-[0.12em] text-brand-gray-light underline underline-offset-2 hover:text-brand-black transition-colors">{{ app()->getLocale() === 'en' ? 'Size Guide' : 'ตารางไซส์' }}</a>
                                </div>
                            </div>
                            <x-size-recommender />
                            <div class="flex flex-wrap gap-2">
                                @foreach($availableSizes as $size)
                                    @php
                                        $variantsForSize = $product->variants->where('size', $size);
                                        $hasStock = $variantsForSize->sum('stock') > 0;
                                    @endphp
                                    <button
                                        @click="selectSize('{{ $size }}')"
                                        :disabled="!isSizeAvailable('{{ $size }}')"
                                        class="min-h-[44px] min-w-[56px] border px-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                                        :class="{
                                            'border-brand-black bg-brand-black text-white': selectedSize === '{{ $size }}',
                                            'border-brand-gray-border text-brand-black hover:border-brand-gray-dark': selectedSize !== '{{ $size }}' && isSizeAvailable('{{ $size }}'),
                                            'border-brand-gray-border text-brand-gray-light line-through cursor-not-allowed opacity-50': !isSizeAvailable('{{ $size }}')
                                        }">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- STOCK INFO -->
                    <div class="mb-6">
                        <template x-if="selectedVariantStock !== null && selectedVariantStock <= 3 && selectedVariantStock > 0">
                            <p class="text-xs uppercase tracking-[0.18em] text-amber-700">
                                {{ app()->getLocale() === 'en' ? 'Only' : 'เหลือเพียง' }}
                                <span x-text="selectedVariantStock" class="font-medium"></span>
                                {{ app()->getLocale() === 'en' ? 'left' : 'ชิ้น' }}
                            </p>
                        </template>
                        <template x-if="selectedVariantStock !== null && selectedVariantStock > 3 && selectedVariantStock <= 10">
                            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-medium">
                                {{ app()->getLocale() === 'en' ? 'Low stock' : 'สต็อกใกล้หมด' }}
                            </p>
                        </template>
                        <template x-if="selectedVariantStock === 0">
                            <div>
                                <p class="text-xs uppercase tracking-[0.18em] text-red-700">{{ app()->getLocale() === 'en' ? 'Out of stock' : 'หมดสต็อก' }}</p>
                                <x-back-in-stock :product="$product" />
                            </div>
                        </template>
                    </div>

                    <!-- QUANTITY + ADD TO CART FORM -->
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedVariantId">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="space-y-5 border-y border-brand-gray-border py-5">
                            <div>
                                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">Design Your Own Shirt</p>
                                <p class="mt-2 text-sm text-brand-gray-dark leading-relaxed">เลือกคอเสื้อ ปลายแขน และกระเป๋าให้เข้ากับวันที่คุณใส่จริง</p>
                            </div>
                            @foreach($customOptionGroups as $groupKey => $group)
                                @php
                                    $defaultOption = $customOptionDefaults[$groupKey] ?? array_key_first($group['options']);
                                @endphp
                                <fieldset>
                                    <legend class="mb-2 text-xs font-medium uppercase tracking-widest text-brand-gray-dark">{{ $group['label'] }}</legend>
                                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                        @foreach($group['options'] as $value => $label)
                                            <label class="custom-option-choice">
                                                <input type="radio"
                                                       name="custom_options[{{ $groupKey }}]"
                                                       value="{{ $value }}"
                                                       class="peer sr-only"
                                                       {{ $defaultOption === $value ? 'checked' : '' }}>
                                                <span class="flex min-h-[44px] items-center justify-center border border-brand-gray-border px-3 text-center text-xs uppercase tracking-[0.08em] transition-colors peer-checked:border-brand-black peer-checked:bg-brand-black peer-checked:text-white hover:border-brand-gray-dark">
                                                    {{ $label }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>

                        <!-- Quantity -->
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">จำนวน</span>
                            <div class="flex items-center border border-brand-gray-border">
                                <button type="button"
                                        @click="quantity = Math.max(1, quantity - 1)" :disabled="quantity <= 1"
                                        class="flex h-11 w-11 items-center justify-center text-brand-gray-dark transition-colors duration-150 hover:bg-brand-gray focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number" name="quantity"
                                       x-model="quantity"
                                       min="1"
                                       class="h-11 w-14 border-x border-brand-gray-border text-center text-sm text-brand-black focus:outline-none focus:ring-0">
                                <button type="button"
                                        @click="quantity = Math.min((selectedVariantStock || 99), quantity + 1)" :disabled="selectedVariantStock !== null && quantity >= selectedVariantStock"
                                        class="flex h-11 w-11 items-center justify-center text-brand-gray-dark transition-colors duration-150 hover:bg-brand-gray focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button type="submit"
                                :disabled="!selectedVariantId || selectedVariantStock === 0"
                                class="w-full py-4 text-sm font-medium tracking-[0.15em] uppercase transition-all duration-300
                                       bg-brand-black text-white hover:bg-brand-gray-dark
                                       disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-brand-black">
                            <template x-if="!selectedVariantId">
                                <span>กรุณาเลือกสีและไซส์</span>
                            </template>
                            <template x-if="selectedVariantId && selectedVariantStock === 0">
                                <span>หมดสต็อก</span>
                            </template>
                            <template x-if="selectedVariantId && selectedVariantStock !== 0">
                                <span>เพิ่มลงตะกร้า</span>
                            </template>
                        </button>
                    </form>

                    <!-- Wishlist Button -->
                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button
                                type="submit"
                                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}"
                                class="w-full py-3.5 text-sm font-medium tracking-[0.15em] uppercase border transition-all duration-300 flex items-center justify-center space-x-2 {{ $inWishlist ? 'border-brand-black text-brand-black bg-brand-gray' : 'border-brand-gray-border text-brand-gray-dark hover:border-brand-black hover:text-brand-black' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                                <span>{{ $inWishlist ? 'ลบออกจาก Wishlist' : 'เพิ่ม Wishlist' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full mt-3 py-3.5 text-sm font-medium tracking-[0.15em] uppercase border border-brand-gray-border text-brand-gray-dark hover:border-brand-black hover:text-brand-black transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <span>เข้าสู่ระบบเพื่อเพิ่ม Wishlist</span>
                        </a>
                    @endauth

                    <div class="mt-6 grid grid-cols-3 border border-brand-gray-border text-center">
                        <a href="{{ route('pages.shipping') }}" class="p-4 border-r border-brand-gray-border">
                            <span class="block text-[10px] uppercase tracking-[0.14em] text-brand-gray-light">Shipping</span>
                            <span class="mt-1 block text-xs">ฟรีทั่วประเทศ</span>
                        </a>
                        <a href="{{ route('pages.returns') }}" class="p-4 border-r border-brand-gray-border">
                            <span class="block text-[10px] uppercase tracking-[0.14em] text-brand-gray-light">Exchange</span>
                            <span class="mt-1 block text-xs">30 วัน</span>
                        </a>
                        <a href="{{ route('pages.member') }}" class="p-4">
                            <span class="block text-[10px] uppercase tracking-[0.14em] text-brand-gray-light">Member</span>
                            <span class="mt-1 block text-xs">สะสมแต้ม</span>
                        </a>
                    </div>

                    <!-- Description -->
                    @if($product->localized_description)
                        <div class="mt-8 pt-6 border-t border-brand-gray-border">
                            <h3 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-3">รายละเอียด</h3>
                            <div class="text-sm text-brand-gray-medium leading-relaxed prose prose-sm max-w-none">
                                {!! nl2br(e($product->localized_description)) !!}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 border-t border-b border-brand-gray-border bg-white" aria-label="CHOMIN shirt details">
            <article class="border-b md:border-b-0 md:border-r border-brand-gray-border">
                <img src="{{ $productEditorialImages['customDetails'] }}"
                     alt="CHO.MIN collar cuff pocket details"
                     class="h-72 w-full object-cover"
                     loading="lazy">
                <div class="p-5">
                    <p class="text-xs uppercase tracking-[0.16em] text-brand-gray-light">Custom details</p>
                    <p class="mt-2 text-sm text-brand-gray-dark">เลือกคอเสื้อ ปลายแขน และกระเป๋าให้เข้ากับลุคที่ต้องใช้จริง</p>
                </div>
            </article>
            <article class="border-b md:border-b-0 md:border-r border-brand-gray-border">
                <img src="{{ $productEditorialImages['duoBox'] }}"
                     alt="CHO.MIN DuoDeal shirts"
                     class="h-72 w-full object-cover"
                     loading="lazy">
                <div class="p-5">
                    <p class="text-xs uppercase tracking-[0.16em] text-brand-gray-light">DuoDeal</p>
                    <p class="mt-2 text-sm text-brand-gray-dark">2 ตัว 1,850 บาท สำหรับเติมสีใหม่เข้าตู้หรือซื้อคู่เป็นของขวัญ</p>
                </div>
            </article>
            <article>
                <img src="{{ $productEditorialImages['careStudio'] }}"
                     alt="CHO.MIN shirt care"
                     class="h-72 w-full object-cover"
                     loading="lazy">
                <div class="p-5">
                    <p class="text-xs uppercase tracking-[0.16em] text-brand-gray-light">Care guide</p>
                    <p class="mt-2 text-sm text-brand-gray-dark">ดูแลทรงและผิวสัมผัสของเชิ้ตให้พร้อมใส่ซ้ำได้บ่อยขึ้น</p>
                </div>
            </article>
        </section>

        {{-- ============================================================
             PRODUCT REVIEWS
        ============================================================ --}}
        <x-product-reviews :product="$product" />

        {{-- ============================================================
             RELATED PRODUCTS
        ============================================================ --}}
        @if($related->isNotEmpty())
            <section class="bg-white border-t border-b border-brand-gray-border" aria-label="Related shirt lines">
                <div class="px-6 md:px-12 py-8 md:py-10 flex items-end justify-between gap-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-3">Related shirt lines</p>
                        <h2 class="font-serif uppercase leading-none text-3xl md:text-5xl text-brand-black">
                            เลือกไลน์อื่น
                        </h2>
                    </div>
                    <a href="{{ route('shop.index') }}" class="hidden sm:inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1 hover:opacity-60">
                        View all
                    </a>
                </div>
                <div class="commerce-grid">
                    @foreach($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============================================================
             COMPLETE THE LOOK
        ============================================================ --}}
        @if(isset($completeTheLook) && $completeTheLook->isNotEmpty())
            <section class="bg-white border-b border-brand-gray-border" aria-label="Complete the look">
                <div class="px-6 md:px-12 py-8 md:py-10">
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-3">Complete the look</p>
                    <h2 class="font-serif uppercase leading-none text-3xl md:text-5xl text-brand-black">
                        จับคู่เซตของคุณ
                    </h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 border-t border-brand-gray-border">
                    @foreach($completeTheLook as $ctl)
                        <x-product-card :product="$ctl" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============================================================
             LIGHTBOX (fullscreen image viewer) — inside x-data scope
        ============================================================ --}}
        <div x-show="lightboxOpen" x-cloak
             class="fixed inset-0 z-[90] flex items-center justify-center bg-black"
             x-transition.opacity
             @keydown.escape.window="closeLightbox()"
             @keydown.arrow-left.window="prevImage()"
             @keydown.arrow-right.window="nextImage()"
             @click.self="closeLightbox()">
            <button type="button" @click="closeLightbox()"
                    class="absolute right-4 top-4 z-10 flex h-12 w-12 items-center justify-center bg-white/10 text-white hover:bg-white/20"
                    aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <template x-if="currentImages.length > 1">
                <button @click.stop="prevImage()"
                        class="absolute left-4 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center bg-white/10 text-white hover:bg-white/20"
                        aria-label="Previous">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
            </template>
            <template x-if="currentImages.length > 1">
                <button @click.stop="nextImage()"
                        class="absolute right-4 top-1/2 z-10 flex h-12 w-12 -translate-y-1/2 items-center justify-center bg-white/10 text-white hover:bg-white/20"
                        aria-label="Next">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </template>
            <img :src="currentImages[activeImageIndex]"
                 alt=""
                 class="max-h-[90vh] max-w-[90vw] object-contain">
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-xs uppercase tracking-[0.2em] text-white/70">
                <span x-text="activeImageIndex + 1"></span> / <span x-text="currentImages.length"></span>
            </div>
        </div>

    </div>

    {{-- ============================================================
         ALPINE.JS: productPage()
    ============================================================ --}}
    <script>
    function productPage() {
        // Build color image map from server data
        const colorImages = @json(
            $product->colors->mapWithKeys(function($color) {
                return [$color->id => $color->images->map(function($img) {
                    return \Illuminate\Support\Facades\Storage::url($img->image_path);
                })->values()];
            })
        );

        // Build variant map: [colorId][size] => {id, stock}
        const variantMap = {};
        @foreach($product->variants as $variant)
        if (!variantMap[{{ $variant->product_color_id ?? 'null' }}]) {
            variantMap[{{ $variant->product_color_id ?? 'null' }}] = {};
        }
        variantMap[{{ $variant->product_color_id ?? 'null' }}]['{{ $variant->size }}'] = {
            id: {{ $variant->id }},
            stock: {{ $variant->stock }}
        };
        @endforeach

        // All product images (fallback)
        const allImages = @json(
            $product->images->map(function($img) {
                return \Illuminate\Support\Facades\Storage::url($img->image_path);
            })->values()
        );

        return {
            selectedColorId: null,
            selectedColorName: '',
            selectedSize: null,
            selectedVariantId: null,
            selectedVariantStock: null,
            activeImageIndex: 0,
            currentImages: allImages.length > 0 ? allImages : [],
            quantity: 1,
            showAllColors: false,
            zoomActive: false,
            zoomX: 50,
            zoomY: 50,
            lightboxOpen: false,

            zoom(event) {
                if (window.innerWidth < 1024) return; // disable on mobile
                const r = event.currentTarget.getBoundingClientRect();
                this.zoomX = ((event.clientX - r.left) / r.width) * 100;
                this.zoomY = ((event.clientY - r.top) / r.height) * 100;
                this.zoomActive = true;
            },
            resetZoom() {
                this.zoomActive = false;
            },
            openLightbox() {
                if (this.currentImages.length === 0) return;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },
            closeLightbox() {
                this.lightboxOpen = false;
                document.body.style.overflow = '';
            },

            init() {
                // Auto-select first color if available
                @if($product->colors->isNotEmpty())
                    const firstColor = {{ $product->colors->first()->id }};
                    const firstName = '{{ $product->colors->first()->localized_name }}';
                    const firstImages = colorImages[firstColor] || allImages;
                    this.selectColor(firstColor, firstName, firstImages);
                @endif
            },

            selectColor(colorId, colorName, images) {
                this.selectedColorId = colorId;
                this.selectedColorName = colorName;
                this.currentImages = images && images.length > 0 ? images : allImages;
                this.activeImageIndex = 0;
                this.selectedSize = null;
                this.selectedVariantId = null;
                this.selectedVariantStock = null;
            },

            selectSize(size) {
                if (!this.isSizeAvailable(size)) return;
                this.selectedSize = size;
                const colorId = this.selectedColorId;
                const key = colorId ? colorId : 'null';
                if (variantMap[key] && variantMap[key][size]) {
                    this.selectedVariantId = variantMap[key][size].id;
                    this.selectedVariantStock = variantMap[key][size].stock;
                } else {
                    // Try without color
                    if (variantMap['null'] && variantMap['null'][size]) {
                        this.selectedVariantId = variantMap['null'][size].id;
                        this.selectedVariantStock = variantMap['null'][size].stock;
                    } else {
                        this.selectedVariantId = null;
                        this.selectedVariantStock = null;
                    }
                }
            },

            isSizeAvailable(size) {
                const colorId = this.selectedColorId;
                const key = colorId ? colorId : 'null';
                if (variantMap[key] && variantMap[key][size] !== undefined) {
                    return variantMap[key][size].stock > 0;
                }
                // If no color selected, check any color
                for (const cKey in variantMap) {
                    if (variantMap[cKey][size] && variantMap[cKey][size].stock > 0) {
                        return true;
                    }
                }
                return false;
            },

            prevImage() {
                if (this.currentImages.length === 0) return;
                this.activeImageIndex = (this.activeImageIndex - 1 + this.currentImages.length) % this.currentImages.length;
            },

            nextImage() {
                if (this.currentImages.length === 0) return;
                this.activeImageIndex = (this.activeImageIndex + 1) % this.currentImages.length;
            },
        };
    }
    </script>

    {{-- Recently viewed (outside x-data scope is fine since it's a server-rendered component) --}}
    <x-recently-viewed :exclude-id="$product->id" />

</x-layouts.shop>
