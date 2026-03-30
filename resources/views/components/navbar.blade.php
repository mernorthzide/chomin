<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-brand-gray-border"
     x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Left: Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="inline-block hover:opacity-80 transition-opacity duration-200">
                    <x-brand-logo variant="dark" class="h-7" />
                </a>
            </div>

            <!-- Center: Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}"
                   class="text-sm font-medium tracking-wide text-brand-gray-dark hover:text-brand-brown transition-colors duration-200 {{ request()->routeIs('home') ? 'text-brand-brown border-b border-brand-brown pb-0.5' : '' }}">
                    หน้าแรก
                </a>
                <a href="{{ route('collections.index') }}"
                   class="text-sm font-medium tracking-wide text-brand-gray-dark hover:text-brand-brown transition-colors duration-200 {{ request()->routeIs('collections.*') ? 'text-brand-brown border-b border-brand-brown pb-0.5' : '' }}">
                    คอลเล็คชัน
                </a>
                <a href="{{ route('shop.index') }}"
                   class="text-sm font-medium tracking-wide text-brand-gray-dark hover:text-brand-brown transition-colors duration-200 {{ request()->routeIs('shop.*') ? 'text-brand-brown border-b border-brand-brown pb-0.5' : '' }}">
                    ร้าน
                </a>
                <a href="{{ route('about') }}"
                   class="text-sm font-medium tracking-wide text-brand-gray-dark hover:text-brand-brown transition-colors duration-200 {{ request()->routeIs('about') ? 'text-brand-brown border-b border-brand-brown pb-0.5' : '' }}">
                    เกี่ยวกับเรา
                </a>
            </div>

            <!-- Right: Icons -->
            <div class="flex items-center space-x-4">
                <!-- User Icon -->
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="text-brand-gray-dark hover:text-brand-brown transition-colors duration-200"
                       title="โปรไฟล์">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-brand-gray-dark hover:text-brand-brown transition-colors duration-200"
                       title="เข้าสู่ระบบ">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @endauth

                <!-- Cart Icon -->
                <a href="#"
                   class="relative text-brand-gray-dark hover:text-brand-brown transition-colors duration-200"
                   title="ตะกร้าสินค้า">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    {{-- Cart count badge — will be wired up in Phase 4 --}}
                    {{-- <span class="absolute -top-1.5 -right-1.5 bg-brand-brown text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">0</span> --}}
                </a>

                <!-- Mobile Hamburger -->
                <button
                    @click="mobileMenu = !mobileMenu"
                    class="md:hidden text-brand-gray-dark hover:text-brand-brown transition-colors duration-200 focus:outline-none"
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
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
            <a href="{{ route('home') }}"
               class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
               @click="mobileMenu = false">
                หน้าแรก
            </a>
            <a href="{{ route('collections.index') }}"
               class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
               @click="mobileMenu = false">
                คอลเล็คชัน
            </a>
            <a href="{{ route('shop.index') }}"
               class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
               @click="mobileMenu = false">
                ร้าน
            </a>
            <a href="{{ route('about') }}"
               class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
               @click="mobileMenu = false">
                เกี่ยวกับเรา
            </a>
            <div class="border-t border-brand-gray-border pt-3 mt-3">
                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
                       @click="mobileMenu = false">
                        โปรไฟล์
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150">
                            ออกจากระบบ
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="block px-3 py-2.5 text-sm font-medium text-brand-gray-dark hover:text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
                       @click="mobileMenu = false">
                        เข้าสู่ระบบ
                    </a>
                    <a href="{{ route('register') }}"
                       class="block px-3 py-2.5 text-sm font-medium text-brand-brown hover:bg-brand-gray rounded transition-colors duration-150"
                       @click="mobileMenu = false">
                        สมัครสมาชิก
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div class="h-16"></div>
