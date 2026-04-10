<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CHOMIN') }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-brand-black" x-data="{ mobileMenu: false }">

    <!-- Announcement Ticker -->
    <div class="bg-brand-black text-white overflow-hidden">
        <div class="ticker-track flex whitespace-nowrap py-2.5">
            @for($i = 0; $i < 3; $i++)
                <span class="ticker-item text-[10px] tracking-[0.2em] uppercase px-8 flex items-center gap-2">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    จัดส่งฟรีทั่วประเทศ
                </span>
                <span class="ticker-item text-[10px] tracking-[0.2em] uppercase px-8 flex items-center gap-2">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    เปลี่ยนคืนภายใน 30 วัน
                </span>
                <span class="ticker-item text-[10px] tracking-[0.2em] uppercase px-8 flex items-center gap-2">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    50+ สี ให้เลือก
                </span>
                <span class="ticker-item text-[10px] tracking-[0.2em] uppercase px-8 flex items-center gap-2">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    ไซส์ XS &ndash; 6XL
                </span>
            @endfor
        </div>
    </div>

    <!-- Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Scroll Reveal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('[data-reveal]');
            if (reveals.length) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
                reveals.forEach(el => observer.observe(el));
            }
        });
    </script>

</body>
</html>
