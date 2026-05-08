@php
    $navCollections = \App\Models\Collection::active()->ordered()->with('translations')->limit(6)->get();
    $navCategories = \App\Models\Category::active()->ordered()->with('translations')->limit(6)->get();
    $navStories = \App\Models\Story::published()->with('translations')->orderByDesc('published_at')->limit(3)->get();
    $segments = request()->segments();
    if (in_array($segments[0] ?? null, config('chomin.locales.supported', ['th', 'en']), true)) {
        array_shift($segments);
    }
    $pathWithoutLocale = implode('/', $segments);
    $queryString = request()->getQueryString();
    $localeUrl = fn (string $locale) => url($locale.($pathWithoutLocale ? '/'.$pathWithoutLocale : '')).($queryString ? '?'.$queryString : '');
@endphp

<nav class="sticky top-0 w-full z-50 bg-white border-b border-brand-gray-border"
     x-data="{ mobileMenu: false, shopMega: false, mobileSection: null }"
     @keydown.escape.window="shopMega = false; mobileMenu = false">
    <div class="flex justify-between items-center px-6 md:px-12 py-4">

        <!-- Left: Desktop Nav Links -->
        <div class="hidden md:flex items-center gap-7 flex-1">
            <div class="relative"
                 @mouseenter="shopMega = true"
                 @mouseleave="shopMega = false"
                 @focusin="shopMega = true">
                <button type="button"
                        @click="shopMega = !shopMega"
                        :aria-expanded="shopMega.toString()"
                        aria-haspopup="true"
                        class="text-xs uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 focus:outline-none focus:underline {{ request()->routeIs('shop.*') || request()->routeIs('sale') || request()->routeIs('color-library') ? 'underline underline-offset-4' : '' }}">
                    ร้าน
                </button>
                <div x-show="shopMega"
                     x-cloak
                     x-transition
                     class="absolute left-0 top-full mt-5 w-[760px] bg-white border border-brand-gray-border shadow-lg p-8"
                     @focusout="if (!$el.contains($event.relatedTarget)) shopMega = false">
                    <div class="grid grid-cols-3 gap-8">
                        <div>
                            <h3 class="text-[11px] font-bold uppercase tracking-[0.18em] mb-4">Shop</h3>
                            <ul class="space-y-3 text-xs uppercase tracking-[0.12em] text-brand-gray-dark">
                                <li><a href="{{ route('shop.index') }}" class="hover:text-brand-black focus:outline-none focus:underline">สินค้าทั้งหมด</a></li>
                                <li><a href="{{ route('sale') }}" class="hover:text-brand-black focus:outline-none focus:underline">Sale</a></li>
                                <li><a href="{{ route('color-library') }}" class="hover:text-brand-black focus:outline-none focus:underline">Color Library</a></li>
                                <li><a href="{{ route('pages.size-guide') }}" class="hover:text-brand-black focus:outline-none focus:underline">Size Guide</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-bold uppercase tracking-[0.18em] mb-4">Collections</h3>
                            <ul class="space-y-3 text-xs uppercase tracking-[0.12em] text-brand-gray-dark">
                                @forelse($navCollections as $collection)
                                    <li><a href="{{ route('collections.show', $collection->slug) }}" class="hover:text-brand-black focus:outline-none focus:underline">{{ $collection->localized_name }}</a></li>
                                @empty
                                    <li><a href="{{ route('collections.index') }}" class="hover:text-brand-black focus:outline-none focus:underline">คอลเล็คชัน</a></li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-bold uppercase tracking-[0.18em] mb-4">Categories</h3>
                            <ul class="space-y-3 text-xs uppercase tracking-[0.12em] text-brand-gray-dark">
                                @foreach($navCategories as $category)
                                    <li><a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="hover:text-brand-black focus:outline-none focus:underline">{{ $category->localized_name }}</a></li>
                                @endforeach
                                @foreach($navStories as $story)
                                    <li><a href="{{ route('stories.show', $story->slug) }}" class="hover:text-brand-black focus:outline-none focus:underline">{{ $story->localized('title') }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('collections.index') }}"
               class="text-xs uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 focus:outline-none focus:underline {{ request()->routeIs('collections.*') ? 'underline underline-offset-4' : '' }}">
                คอลเล็คชัน
            </a>
            <a href="{{ route('stories.index') }}"
               class="text-xs uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 focus:outline-none focus:underline {{ request()->routeIs('stories.*') ? 'underline underline-offset-4' : '' }}">
                บทความ
            </a>
            <a href="{{ route('about') }}"
               class="text-xs uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 focus:outline-none focus:underline {{ request()->routeIs('about') ? 'underline underline-offset-4' : '' }}">
                เกี่ยวกับเรา
            </a>
        </div>

        <!-- Center: Logo -->
        <div class="flex-shrink-0 md:absolute md:left-1/2 md:-translate-x-1/2">
            <a href="{{ route('home') }}" class="block hover:opacity-80 transition-opacity duration-200">
                <x-brand-logo variant="dark" class="h-7 md:h-10" />
            </a>
        </div>

        <!-- Right: Icons -->
        <div class="flex items-center gap-5 flex-1 justify-end">
            <a href="{{ route('search') }}"
               class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 rounded-sm"
               title="ค้นหา">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </a>
            <div class="hidden md:flex items-center gap-2 text-[11px] uppercase tracking-[0.14em] text-brand-gray-medium">
                <a href="{{ $localeUrl('th') }}" class="{{ app()->getLocale() === 'th' ? 'text-brand-black underline underline-offset-4' : 'hover:text-brand-black' }}">TH</a>
                <span>/</span>
                <a href="{{ $localeUrl('en') }}" class="{{ app()->getLocale() === 'en' ? 'text-brand-black underline underline-offset-4' : 'hover:text-brand-black' }}">EN</a>
            </div>
            <!-- User Icon -->
            @auth
                <a href="{{ route('profile.edit') }}"
                   class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 rounded-sm"
                   title="โปรไฟล์">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="hidden md:block text-xs uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 focus:outline-none focus:underline"
                   title="เข้าสู่ระบบ">
                    เข้าสู่ระบบ
                </a>
                <a href="{{ route('login') }}"
                   class="md:hidden hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 rounded-sm"
                   title="เข้าสู่ระบบ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
            @endauth

            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}"
               class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2 rounded-sm"
               title="ตะกร้าสินค้า">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </a>

            <!-- Mobile Hamburger -->
            <button
                @click="mobileMenu = !mobileMenu"
                class="md:hidden hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2"
                :aria-expanded="mobileMenu.toString()"
                aria-label="เมนู">
                <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="mobileMenu" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div
        x-show="mobileMenu"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="md:hidden bg-white border-t border-brand-gray-border shadow-sm">
        <div class="px-6 py-4 space-y-1">
            <a href="{{ route('home') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
               @click="mobileMenu = false">
                หน้าแรก
            </a>
            <button type="button"
                    @click="mobileSection = mobileSection === 'shop' ? null : 'shop'"
                    class="w-full flex items-center justify-between py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline">
                <span>ร้าน</span>
                <span aria-hidden="true" x-text="mobileSection === 'shop' ? '-' : '+'"></span>
            </button>
            <div x-show="mobileSection === 'shop'" x-transition class="pb-2 pl-4 space-y-2">
                <a href="{{ route('shop.index') }}" class="block py-1.5 text-xs uppercase tracking-[0.12em] text-brand-gray-medium" @click="mobileMenu = false">สินค้าทั้งหมด</a>
                <a href="{{ route('sale') }}" class="block py-1.5 text-xs uppercase tracking-[0.12em] text-brand-gray-medium" @click="mobileMenu = false">Sale</a>
                <a href="{{ route('color-library') }}" class="block py-1.5 text-xs uppercase tracking-[0.12em] text-brand-gray-medium" @click="mobileMenu = false">Color Library</a>
                <a href="{{ route('pages.size-guide') }}" class="block py-1.5 text-xs uppercase tracking-[0.12em] text-brand-gray-medium" @click="mobileMenu = false">Size Guide</a>
            </div>
            <a href="{{ route('collections.index') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
               @click="mobileMenu = false">
                คอลเล็คชัน
            </a>
            <a href="{{ route('stories.index') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
               @click="mobileMenu = false">
                บทความ
            </a>
            <a href="{{ route('search') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
               @click="mobileMenu = false">
                ค้นหา
            </a>
            <a href="{{ route('about') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
               @click="mobileMenu = false">
                เกี่ยวกับเรา
            </a>
            <div class="flex gap-4 py-2.5 text-xs uppercase tracking-[0.14em]">
                <a href="{{ $localeUrl('th') }}" class="{{ app()->getLocale() === 'th' ? 'text-brand-black underline underline-offset-4' : 'text-brand-gray-medium' }}">TH</a>
                <a href="{{ $localeUrl('en') }}" class="{{ app()->getLocale() === 'en' ? 'text-brand-black underline underline-offset-4' : 'text-brand-gray-medium' }}">EN</a>
            </div>
            <div class="border-t border-brand-gray-border pt-3 mt-3">
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
                       @click="mobileMenu = false">
                        โปรไฟล์
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150">
                            ออกจากระบบ
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-black transition-colors duration-150 focus:outline-none focus:underline"
                       @click="mobileMenu = false">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}"
                       class="block py-2.5 text-sm font-medium text-brand-black font-bold transition-colors duration-150"
                       @click="mobileMenu = false">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
