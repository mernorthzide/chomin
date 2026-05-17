<div
    x-data="quickView()"
    x-init="init()"
    @open-quick-view.window="open($event.detail.slug)"
    @keydown.escape.window="close()"
>
    <div x-show="isOpen" x-cloak
         class="fixed inset-0 z-[80] flex items-center justify-center bg-brand-black/60 px-3 py-6 md:px-8"
         x-transition.opacity
         @click.self="close()">

        <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden bg-white md:flex-row"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <button type="button" @click="close()"
                    class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center bg-white/90 text-brand-black hover:bg-white"
                    aria-label="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <template x-if="loading">
                <div class="flex h-96 w-full items-center justify-center">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-black border-t-transparent"></div>
                </div>
            </template>

            <template x-if="!loading && product">
                <div class="flex w-full flex-col md:flex-row">
                    {{-- Image gallery --}}
                    <div class="relative w-full md:w-1/2">
                        <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray">
                            <template x-if="product.images.length > 0">
                                <img :src="product.images[activeImage]" :alt="product.name"
                                     class="h-full w-full object-cover">
                            </template>
                            <template x-if="product.images.length === 0">
                                <div class="flex h-full w-full items-center justify-center">
                                    <span class="font-serif text-5xl text-brand-gray-border">CHO</span>
                                </div>
                            </template>
                        </div>
                        <template x-if="product.images.length > 1">
                            <div class="flex gap-2 overflow-x-auto p-3">
                                <template x-for="(img, i) in product.images" :key="i">
                                    <button type="button" @click="activeImage = i"
                                            class="h-16 w-12 flex-shrink-0 overflow-hidden border"
                                            :class="activeImage === i ? 'border-brand-black' : 'border-brand-gray-border'">
                                        <img :src="img" :alt="product.name" class="h-full w-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Details --}}
                    <div class="flex w-full flex-col gap-5 overflow-y-auto p-6 md:w-1/2 md:p-8">
                        <div>
                            <template x-if="product.collection">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-brand-gray-light" x-text="product.collection"></p>
                            </template>
                            <h2 class="mt-2 font-serif text-2xl uppercase leading-tight" x-text="product.name"></h2>

                            <div class="mt-3 flex items-baseline gap-3">
                                <span class="text-lg" x-text="'฿' + product.display_price.toLocaleString()"></span>
                                <template x-if="product.is_on_sale">
                                    <span class="text-sm text-brand-gray-light line-through" x-text="'฿' + product.price.toLocaleString()"></span>
                                </template>
                            </div>
                        </div>

                        <template x-if="product.description">
                            <p class="text-sm leading-relaxed text-brand-gray-medium line-clamp-4" x-text="product.description"></p>
                        </template>

                        {{-- Colors --}}
                        <template x-if="product.colors.length > 0">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">
                                    {{ app()->getLocale() === 'en' ? 'Color' : 'สี' }}
                                </p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <template x-for="color in product.colors" :key="color.slug">
                                        <button type="button" @click="selectedColor = color.slug"
                                                class="h-8 w-8 rounded-full border"
                                                :class="selectedColor === color.slug ? 'border-brand-black ring-2 ring-brand-black ring-offset-2' : 'border-brand-gray-border'"
                                                :style="`background-color: ${color.code || '#eeeeee'}`"
                                                :title="color.name"
                                                :aria-label="color.name"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Sizes --}}
                        <template x-if="product.sizes.length > 0">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">
                                    {{ app()->getLocale() === 'en' ? 'Size' : 'ไซส์' }}
                                </p>
                                <div class="mt-2 grid grid-cols-5 gap-2">
                                    <template x-for="s in product.sizes" :key="s.size">
                                        <button type="button" @click="s.stock > 0 && (selectedSize = s.size)"
                                                :disabled="s.stock === 0"
                                                class="border py-2 text-xs uppercase"
                                                :class="[
                                                    selectedSize === s.size ? 'border-brand-black bg-brand-black text-white' : 'border-brand-gray-border',
                                                    s.stock === 0 ? 'opacity-40 line-through cursor-not-allowed' : 'hover:border-brand-black'
                                                ]"
                                                x-text="s.size"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="mt-auto flex flex-col gap-2 pt-4">
                            <a :href="product.url"
                               class="block w-full bg-brand-black px-4 py-3 text-center text-xs uppercase tracking-[0.16em] text-white hover:opacity-90">
                                {{ app()->getLocale() === 'en' ? 'View full details' : 'ดูรายละเอียดทั้งหมด' }}
                            </a>
                            <button type="button" @click="close()"
                                    class="block w-full border border-brand-black px-4 py-3 text-center text-xs uppercase tracking-[0.16em] hover:bg-brand-black hover:text-white">
                                {{ app()->getLocale() === 'en' ? 'Close' : 'ปิด' }}
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function quickView() {
    return {
        isOpen: false,
        loading: false,
        product: null,
        activeImage: 0,
        selectedColor: null,
        selectedSize: null,
        init() {},
        async open(slug) {
            this.isOpen = true;
            this.loading = true;
            this.product = null;
            this.activeImage = 0;
            this.selectedColor = null;
            this.selectedSize = null;
            document.body.style.overflow = 'hidden';
            try {
                const locale = document.documentElement.lang || 'th';
                const res = await fetch(`/${locale}/products/${slug}/quickview`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed to load');
                this.product = await res.json();
            } catch (e) {
                console.error(e);
                this.close();
            } finally {
                this.loading = false;
            }
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        }
    };
}
</script>
