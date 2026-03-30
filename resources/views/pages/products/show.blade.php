<x-layouts.shop>

    <div
        x-data="productPage()"
        x-init="init()"
        class="bg-white">

        {{-- ============================================================
             BREADCRUMB
        ============================================================ --}}
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <ol class="flex items-center space-x-2 text-xs text-brand-gray-medium">
                <li><a href="{{ route('home') }}" class="hover:text-brand-black transition-colors">หน้าแรก</a></li>
                <li><span class="text-brand-gray-border">/</span></li>
                @if($product->collection)
                    <li><a href="{{ route('collections.show', $product->collection->slug) }}" class="hover:text-brand-black transition-colors">{{ $product->collection->name }}</a></li>
                    <li><span class="text-brand-gray-border">/</span></li>
                @endif
                <li class="text-brand-black truncate max-w-[200px]">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- ============================================================
             MAIN PRODUCT SECTION
        ============================================================ --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 md:pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">

                {{-- ---- LEFT: IMAGE GALLERY ---- --}}
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray">
                        <template x-if="currentImages.length > 0">
                            <img
                                :src="currentImages[activeImageIndex]"
                                :alt="'{{ $product->name }}'"
                                class="w-full h-full object-cover transition-opacity duration-300"
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
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white flex items-center justify-center transition-colors duration-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="nextImage()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-white/80 hover:bg-white flex items-center justify-center transition-colors duration-200 shadow-sm">
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
                                <img :src="img" class="w-full h-full object-cover" alt="">
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
                    <h1 class="text-2xl md:text-3xl font-medium text-brand-black leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Price -->
                    <p class="mt-3 text-xl font-normal text-brand-black">
                        ฿{{ number_format($product->price, 0) }}
                    </p>

                    <!-- Divider -->
                    <div class="w-12 h-px bg-brand-gray-border my-6"></div>

                    @if($product->colors->isNotEmpty())
                        <!-- COLOR SELECTION -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">สี</span>
                                <span class="text-xs text-brand-gray-medium" x-text="selectedColorName"></span>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                @foreach($product->colors as $color)
                                    <button
                                        @click="selectColor({{ $color->id }}, '{{ $color->name }}', {{ json_encode($color->images->pluck('image_path')->map(fn($p) => \Illuminate\Support\Facades\Storage::url($p))->values()) }})"
                                        class="w-8 h-8 rounded-full border-2 transition-all duration-200 focus:outline-none"
                                        :class="{{ $color->id }} === selectedColorId ? 'border-brand-black scale-110 shadow-md' : 'border-brand-gray-border hover:border-brand-gray-dark'"
                                        style="background-color: {{ $color->color_code ?? '#cccccc' }}"
                                        :title="'{{ $color->name }}'">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- SIZE SELECTION -->
                    @php
                        $availableSizes = $product->variants->when(
                            request()->has('color') || true,
                            fn($v) => $v
                        )->pluck('size')->unique()->sort()->values();
                    @endphp

                    @if($availableSizes->isNotEmpty())
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">ไซส์</span>
                                <span class="text-xs text-brand-gray-medium" x-text="selectedSize ? selectedSize : 'กรุณาเลือก'"></span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($availableSizes as $size)
                                    @php
                                        $variantsForSize = $product->variants->where('size', $size);
                                        $hasStock = $variantsForSize->sum('stock') > 0;
                                    @endphp
                                    <button
                                        @click="selectSize('{{ $size }}')"
                                        :disabled="!isSizeAvailable('{{ $size }}')"
                                        class="min-w-[48px] h-10 px-3 text-sm border transition-all duration-200 focus:outline-none"
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
                        <template x-if="selectedVariantStock !== null && selectedVariantStock <= 5 && selectedVariantStock > 0">
                            <p class="text-xs text-amber-600 tracking-wide">เหลือเพียง <span x-text="selectedVariantStock"></span> ชิ้น</p>
                        </template>
                        <template x-if="selectedVariantStock === 0">
                            <p class="text-xs text-red-500 tracking-wide">หมดสต็อก</p>
                        </template>
                    </div>

                    <!-- QUANTITY + ADD TO CART FORM -->
                    <form action="/cart/add" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedVariantId">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <!-- Quantity -->
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark">จำนวน</span>
                            <div class="flex items-center border border-brand-gray-border">
                                <button type="button"
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        class="w-10 h-10 flex items-center justify-center text-brand-gray-dark hover:bg-brand-gray transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number" name="quantity"
                                       x-model="quantity"
                                       min="1"
                                       class="w-12 h-10 text-center border-x border-brand-gray-border text-sm text-brand-black focus:outline-none focus:ring-0">
                                <button type="button"
                                        @click="quantity++"
                                        class="w-10 h-10 flex items-center justify-center text-brand-gray-dark hover:bg-brand-gray transition-colors duration-150">
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
                                       bg-brand-black text-white hover:bg-brand-brown
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
                    <button
                        type="button"
                        class="w-full mt-3 py-3.5 text-sm font-medium tracking-[0.15em] uppercase border border-brand-gray-border text-brand-gray-dark hover:border-brand-black hover:text-brand-black transition-all duration-300 flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        <span>เพิ่ม Wishlist</span>
                    </button>

                    <!-- Description -->
                    @if($product->description)
                        <div class="mt-8 pt-6 border-t border-brand-gray-border">
                            <h3 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-3">รายละเอียด</h3>
                            <div class="text-sm text-brand-gray-medium leading-relaxed prose prose-sm max-w-none">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>

        {{-- ============================================================
             RELATED PRODUCTS
        ============================================================ --}}
        @if($related->isNotEmpty())
            <section class="bg-brand-gray py-14 md:py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="font-serif text-2xl md:text-3xl font-normal text-brand-black uppercase tracking-widest mb-10 text-center">
                        สินค้าที่เกี่ยวข้อง
                    </h2>
                    <div class="flex gap-4 md:gap-6 overflow-x-auto pb-4 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 sm:overflow-visible">
                        @foreach($related as $relatedProduct)
                            <div class="flex-shrink-0 w-48 sm:w-auto snap-start">
                                <x-product-card :product="$relatedProduct" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

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

            init() {
                // Auto-select first color if available
                @if($product->colors->isNotEmpty())
                    const firstColor = {{ $product->colors->first()->id }};
                    const firstName = '{{ $product->colors->first()->name }}';
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

</x-layouts.shop>
