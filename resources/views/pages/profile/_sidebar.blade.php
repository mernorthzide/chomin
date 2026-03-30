<nav class="bg-white border border-brand-gray-border">
    <div class="px-4 py-4 border-b border-brand-gray-border">
        <p class="text-xs font-medium tracking-widest uppercase text-brand-gray-medium">บัญชีของฉัน</p>
        <p class="mt-1 text-sm font-medium text-brand-black">{{ auth()->user()->name }}</p>
    </div>
    <ul class="divide-y divide-brand-gray-border">
        <li>
            <a href="{{ route('profile.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-150 {{ request()->routeIs('profile.index') ? 'text-brand-black font-medium bg-brand-gray' : 'text-brand-gray-dark hover:bg-brand-gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                ข้อมูลส่วนตัว
            </a>
        </li>
        <li>
            <a href="{{ route('addresses.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-150 {{ request()->routeIs('addresses.*') ? 'text-brand-black font-medium bg-brand-gray' : 'text-brand-gray-dark hover:bg-brand-gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                ที่อยู่จัดส่ง
            </a>
        </li>
        <li>
            <a href="{{ route('orders.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-150 {{ request()->routeIs('orders.*') ? 'text-brand-black font-medium bg-brand-gray' : 'text-brand-gray-dark hover:bg-brand-gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                ประวัติสั่งซื้อ
            </a>
        </li>
        <li>
            <a href="{{ route('profile.points') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-150 {{ request()->routeIs('profile.points') ? 'text-brand-black font-medium bg-brand-gray' : 'text-brand-gray-dark hover:bg-brand-gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                แต้มสะสม
                <span class="ml-auto text-xs font-medium text-brand-black bg-brand-gray-border px-2 py-0.5 rounded-full">
                    {{ number_format(auth()->user()->points) }}
                </span>
            </a>
        </li>
        <li>
            <a href="{{ route('wishlist.index') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-150 {{ request()->routeIs('wishlist.*') ? 'text-brand-black font-medium bg-brand-gray' : 'text-brand-gray-dark hover:bg-brand-gray' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Wishlist
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition-colors duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    ออกจากระบบ
                </button>
            </form>
        </li>
    </ul>
</nav>
