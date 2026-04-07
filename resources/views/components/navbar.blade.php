<nav class="sticky top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-brand-gray-border/50"
     x-data="{ mobileMenu: false }">
    <div class="flex justify-between items-center px-6 md:px-12 py-4">

        <!-- Left: Desktop Nav Links -->
        <div class="hidden md:flex items-center gap-7 flex-1">
            <a href="{{ route('shop.index') }}"
               class="text-[11px] uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 {{ request()->routeIs('shop.*') ? 'text-brand-brown' : '' }}">
                ร้าน
            </a>
            <a href="{{ route('collections.index') }}"
               class="text-[11px] uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 {{ request()->routeIs('collections.*') ? 'text-brand-brown' : '' }}">
                คอลเล็คชัน
            </a>
            <a href="{{ route('about') }}"
               class="text-[11px] uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200 {{ request()->routeIs('about') ? 'text-brand-brown' : '' }}">
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
            <!-- User Icon -->
            @auth
                <a href="{{ route('profile.edit') }}"
                   class="hover:opacity-60 transition-opacity duration-200"
                   title="โปรไฟล์">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="hidden md:block text-[11px] uppercase tracking-[0.15em] font-semibold hover:text-brand-gray-medium transition-colors duration-200"
                   title="เข้าสู่ระบบ">
                    เข้าสู่ระบบ
                </a>
                <a href="{{ route('login') }}"
                   class="md:hidden hover:opacity-60 transition-opacity duration-200"
                   title="เข้าสู่ระบบ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
            @endauth

            <!-- Search Icon -->
            <a href="{{ route('shop.index') }}"
               class="hover:opacity-60 transition-opacity duration-200"
               title="ค้นหา">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </a>

            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}"
               class="hover:opacity-60 transition-opacity duration-200"
               title="ตะกร้าสินค้า">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </a>

            <!-- Mobile Hamburger -->
            <button
                @click="mobileMenu = !mobileMenu"
                class="md:hidden hover:opacity-60 transition-opacity duration-200 focus:outline-none"
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
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
               @click="mobileMenu = false">
                หน้าแรก
            </a>
            <a href="{{ route('collections.index') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
               @click="mobileMenu = false">
                คอลเล็คชัน
            </a>
            <a href="{{ route('shop.index') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
               @click="mobileMenu = false">
                ร้าน
            </a>
            <a href="{{ route('about') }}"
               class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
               @click="mobileMenu = false">
                เกี่ยวกับเรา
            </a>
            <div class="border-t border-brand-gray-border pt-3 mt-3">
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
                       @click="mobileMenu = false">
                        โปรไฟล์
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150">
                            ออกจากระบบ
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown transition-colors duration-150"
                       @click="mobileMenu = false">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}"
                       class="block py-2.5 text-sm font-medium text-brand-brown transition-colors duration-150"
                       @click="mobileMenu = false">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
