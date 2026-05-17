@php
    $navCollections = $navCollections ?? [];
    $navCategories = $navCategories ?? [];
    $segments = request()->segments();
    if (in_array($segments[0] ?? null, config('chomin.locales.supported', ['th', 'en']), true)) {
        array_shift($segments);
    }
    $pathWithoutLocale = implode('/', $segments);
    $queryString = request()->getQueryString();
    $localeUrl = fn (string $locale) => url($locale.($pathWithoutLocale ? '/'.$pathWithoutLocale : '')).($queryString ? '?'.$queryString : '');
@endphp

<nav class="sticky top-0 z-50 border-b border-brand-gray-border bg-white"
     x-data="{ mobileMenu: false, shopMega: false, mobileSection: null, searchOpen: false }"
     @keydown.escape.window="shopMega = false; mobileMenu = false; searchOpen = false">
    <div class="relative flex items-center justify-between px-4 md:px-8 py-3">
        <div class="flex items-center gap-5">
            <button type="button"
                    class="inline-flex h-11 w-11 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                    @click="mobileMenu = !mobileMenu"
                    :aria-expanded="mobileMenu.toString()"
                    aria-label="เมนู">
                <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg x-show="mobileMenu" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <button type="button"
                    @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())"
                    class="inline-flex h-11 w-11 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                    aria-label="{{ app()->getLocale() === 'en' ? 'Search' : 'ค้นหา' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m0 0A7.5 7.5 0 1 0 5.2 5.2a7.5 7.5 0 0 0 10.6 10.6Z" />
                </svg>
            </button>

            <div class="hidden lg:flex items-center gap-5 text-xs uppercase tracking-[0.14em]">
                <div class="relative"
                     @mouseenter="shopMega = true"
                     @mouseleave="shopMega = false"
                     @focusin="shopMega = true">
                    <button type="button"
                            @click="shopMega = !shopMega"
                            :aria-expanded="shopMega.toString()"
                            aria-haspopup="true"
                            class="inline-flex min-h-11 items-center hover:opacity-60 focus:outline-none focus:underline">
                        Products
                    </button>
                    <div x-show="shopMega"
                         x-cloak
                         x-transition
                         class="absolute left-0 top-full mt-4 w-[760px] border border-brand-gray-border bg-white p-7 shadow-xl"
                         @focusout="if (!$el.contains($event.relatedTarget)) shopMega = false">
                        <div class="grid grid-cols-3 gap-8">
                            <div>
                                <h3 class="mb-4 text-[11px] uppercase tracking-[0.18em] text-brand-gray-light">Shop</h3>
                                <ul class="space-y-3 text-xs uppercase tracking-[0.12em]">
                                    <li><a href="{{ route('shop.index') }}" class="hover:opacity-60">All products</a></li>
                                    <li><a href="{{ route('sale') }}" class="hover:opacity-60">Special Price</a></li>
                                    <li><a href="{{ route('color-library') }}" class="hover:opacity-60">Color Library</a></li>
                                    <li><a href="{{ route('pages.size-guide') }}" class="hover:opacity-60">Size Guide</a></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="mb-4 text-[11px] uppercase tracking-[0.18em] text-brand-gray-light">Collections</h3>
                                <ul class="space-y-3 text-xs uppercase tracking-[0.12em]">
                                    @forelse($navCollections as $collection)
                                        <li><a href="{{ route('collections.show', $collection['slug']) }}" class="hover:opacity-60">{{ $collection['name'] }}</a></li>
                                    @empty
                                        <li><a href="{{ route('collections.index') }}" class="hover:opacity-60">Collections</a></li>
                                    @endforelse
                                </ul>
                            </div>
                            <div>
                                <h3 class="mb-4 text-[11px] uppercase tracking-[0.18em] text-brand-gray-light">Categories</h3>
                                <ul class="space-y-3 text-xs uppercase tracking-[0.12em]">
                                    @forelse($navCategories as $category)
                                        <li><a href="{{ route('shop.index', ['category' => $category['slug']]) }}" class="hover:opacity-60">{{ $category['name'] }}</a></li>
                                    @empty
                                        <li><a href="{{ route('shop.index') }}" class="hover:opacity-60">Shirts</a></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('collections.index') }}" class="hover:opacity-60">Collections</a>
                <a href="{{ route('sale') }}" class="hover:opacity-60">Special Price</a>
                <a href="{{ route('pages.member') }}" class="hover:opacity-60">Member</a>
            </div>
        </div>

        <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2 block hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
            <x-brand-logo variant="dark" class="h-7 md:h-9" />
        </a>

        <a href="{{ route('cart.index') }}"
           class="absolute right-4 top-1/2 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 md:hidden"
           aria-label="ตะกร้าสินค้า {{ $cartCount > 0 ? "($cartCount)" : '' }}">
            <span class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.36-1.99 1.26 12c.07.66-.45 1.24-1.12 1.24H4.25a1.13 1.13 0 0 1-1.12-1.24l1.26-12A1.13 1.13 0 0 1 5.51 7.5h12.98c.57 0 1.06.44 1.12 1.01Z" />
                </svg>
                @if($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-black text-white text-[9px] font-medium leading-none">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                @endif
            </span>
        </a>

        <div class="hidden items-center justify-end gap-3 md:flex md:gap-4">
            <div class="flex items-center gap-2 text-[11px] uppercase tracking-[0.14em] text-brand-gray-medium">
                <a href="{{ $localeUrl('th') }}" class="{{ app()->getLocale() === 'th' ? 'text-brand-black underline underline-offset-4' : 'hover:text-brand-black' }}">TH</a>
                <span>/</span>
                <a href="{{ $localeUrl('en') }}" class="{{ app()->getLocale() === 'en' ? 'text-brand-black underline underline-offset-4' : 'hover:text-brand-black' }}">EN</a>
            </div>

            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}"
               class="inline-flex h-11 w-11 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
               aria-label="บัญชี">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0A17.93 17.93 0 0 1 12 21.75c-2.68 0-5.22-.58-7.5-1.63Z" />
                </svg>
            </a>

            <a href="{{ route('wishlist.index') }}"
               class="hidden h-11 w-11 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 sm:inline-flex"
               aria-label="Wishlist">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.93 0-3.6 1.13-4.31 2.73-.72-1.6-2.38-2.73-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </a>

            <a href="{{ route('cart.index') }}"
               class="inline-flex h-11 w-11 items-center justify-center hover:opacity-60 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
               aria-label="ตะกร้าสินค้า {{ $cartCount > 0 ? "($cartCount)" : '' }}">
                <span class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.36-1.99 1.26 12c.07.66-.45 1.24-1.12 1.24H4.25a1.13 1.13 0 0 1-1.12-1.24l1.26-12A1.13 1.13 0 0 1 5.51 7.5h12.98c.57 0 1.06.44 1.12 1.01Z" />
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-black text-white text-[9px] font-medium leading-none">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                    @endif
                </span>
            </a>
        </div>
    </div>

    <div x-show="mobileMenu"
         x-cloak
         x-transition
         class="border-t border-brand-gray-border bg-white lg:hidden">
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 gap-1 text-sm">
                <a href="{{ route('shop.index') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Products</a>
                <a href="{{ route('collections.index') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Collections</a>
                <a href="{{ route('sale') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Special Price</a>
                <a href="{{ route('color-library') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Color Library</a>
                <a href="{{ route('pages.size-guide') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Size Guide</a>
                <a href="{{ route('pages.member') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Member</a>
                <a href="{{ route('stories.index') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">Stories</a>
                <a href="{{ route('about') }}" class="py-3 uppercase tracking-[0.12em]" @click="mobileMenu = false">About</a>
            </div>

            <div class="mt-5 flex gap-4 border-t border-brand-gray-border pt-5 text-xs uppercase tracking-[0.14em]">
                <a href="{{ $localeUrl('th') }}" class="{{ app()->getLocale() === 'th' ? 'underline underline-offset-4' : 'text-brand-gray-medium' }}">TH</a>
                <a href="{{ $localeUrl('en') }}" class="{{ app()->getLocale() === 'en' ? 'underline underline-offset-4' : 'text-brand-gray-medium' }}">EN</a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SEARCH OVERLAY w/ AUTOCOMPLETE
    ============================================================ --}}
    <div x-show="searchOpen" x-cloak
         x-transition.opacity
         class="fixed inset-0 z-[70] bg-black/40"
         @click.self="searchOpen = false"></div>

    <div x-show="searchOpen" x-cloak
         x-transition.opacity
         class="fixed top-0 left-0 right-0 z-[71] border-b border-brand-gray-border bg-white"
         x-data="searchAutocomplete()"
         @keydown.escape.window="searchOpen = false">
        <form :action="'/' + (document.documentElement.lang || 'th') + '/search'" method="GET"
              class="max-w-5xl mx-auto px-4 md:px-8 py-4 flex items-center gap-3">
            <svg class="h-5 w-5 text-brand-gray-medium shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m0 0A7.5 7.5 0 1 0 5.2 5.2a7.5 7.5 0 0 0 10.6 10.6Z"/>
            </svg>
            <input type="search" name="q" x-ref="searchInput" x-model="query"
                   @input.debounce.250ms="fetchResults()"
                   placeholder="{{ app()->getLocale() === 'en' ? 'Search shirts, colors, collections…' : 'ค้นหาเชิ้ต สี คอลเลกชัน…' }}"
                   class="w-full border-0 bg-transparent text-base focus:outline-none focus:ring-0"
                   autocomplete="off">
            <button type="button" @click="searchOpen = false"
                    class="text-xs uppercase tracking-[0.14em] text-brand-gray-medium hover:text-brand-black">
                {{ app()->getLocale() === 'en' ? 'Close' : 'ปิด' }}
            </button>
        </form>

        <div x-show="query.length >= 2" class="max-w-5xl mx-auto px-4 md:px-8 pb-6 border-t border-brand-gray-border">
            <template x-if="loading">
                <p class="py-6 text-xs uppercase tracking-[0.14em] text-brand-gray-light">{{ app()->getLocale() === 'en' ? 'Searching…' : 'กำลังค้นหา…' }}</p>
            </template>

            <template x-if="!loading && products.length === 0 && collections.length === 0">
                <p class="py-6 text-xs uppercase tracking-[0.14em] text-brand-gray-light">
                    {{ app()->getLocale() === 'en' ? 'No matches. Try another keyword.' : 'ไม่พบผลลัพธ์ ลองคำอื่น' }}
                </p>
            </template>

            <template x-if="!loading && collections.length > 0">
                <div class="pt-5">
                    <p class="text-[10px] uppercase tracking-[0.18em] text-brand-gray-light mb-3">{{ app()->getLocale() === 'en' ? 'Collections' : 'คอลเลกชัน' }}</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="c in collections" :key="'c-'+c.id">
                            <a :href="c.url" class="border border-brand-gray-border px-3 py-2 text-xs uppercase tracking-[0.14em] hover:bg-brand-black hover:text-white hover:border-brand-black" x-text="c.name"></a>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="!loading && products.length > 0">
                <div class="pt-5">
                    <p class="text-[10px] uppercase tracking-[0.18em] text-brand-gray-light mb-3">{{ app()->getLocale() === 'en' ? 'Products' : 'สินค้า' }}</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <template x-for="p in products" :key="'p-'+p.id">
                            <a :href="p.url" class="flex items-center gap-3 border border-brand-gray-border p-2 hover:border-brand-black">
                                <div class="w-14 h-16 bg-brand-gray shrink-0 overflow-hidden">
                                    <template x-if="p.image">
                                        <img :src="p.image" :alt="p.name" class="w-full h-full object-cover">
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] uppercase tracking-[0.14em] text-brand-gray-light truncate" x-text="p.collection || ''"></p>
                                    <p class="text-sm truncate" x-text="p.name"></p>
                                    <p class="text-xs font-medium mt-0.5" x-text="'฿' + Math.round(p.price).toLocaleString()"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                    <a :href="viewAllUrl" class="mt-4 inline-block text-xs uppercase tracking-[0.14em] underline">
                        {{ app()->getLocale() === 'en' ? 'See all results' : 'ดูผลทั้งหมด' }}
                    </a>
                </div>
            </template>
        </div>

        <div x-show="query.length < 2" class="max-w-5xl mx-auto px-4 md:px-8 pb-6 border-t border-brand-gray-border">
            <p class="pt-5 text-[10px] uppercase tracking-[0.18em] text-brand-gray-light mb-3">{{ app()->getLocale() === 'en' ? 'Popular' : 'ยอดนิยม' }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach(['ขาว', 'ดำ', 'Classic', 'Oversize', 'Linen', 'Bestseller'] as $term)
                    <button type="button" @click="query = '{{ $term }}'; $refs.searchInput.focus(); fetchResults()"
                            class="border border-brand-gray-border px-3 py-2 text-xs uppercase tracking-[0.14em] hover:bg-brand-black hover:text-white hover:border-brand-black">
                        {{ $term }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
    function searchAutocomplete() {
        return {
            query: '',
            loading: false,
            products: [],
            collections: [],
            viewAllUrl: '',
            abortController: null,
            async fetchResults() {
                if (this.query.length < 2) {
                    this.products = []; this.collections = []; this.loading = false;
                    return;
                }
                if (this.abortController) this.abortController.abort();
                this.abortController = new AbortController();
                this.loading = true;
                try {
                    const locale = document.documentElement.lang || 'th';
                    const url = `/${locale}/search/autocomplete?q=${encodeURIComponent(this.query)}`;
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' },
                        signal: this.abortController.signal
                    });
                    if (!res.ok) throw new Error('Search failed');
                    const data = await res.json();
                    this.products = data.products || [];
                    this.collections = data.collections || [];
                    this.viewAllUrl = data.view_all_url || `/${locale}/search?q=${encodeURIComponent(this.query)}`;
                } catch (e) {
                    if (e.name !== 'AbortError') console.error(e);
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    </script>
</nav>
