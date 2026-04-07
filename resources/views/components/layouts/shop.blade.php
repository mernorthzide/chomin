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
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    รับประกันคุณภาพตลอดอายุการใช้งาน
                </span>
                <span class="ticker-item text-[10px] tracking-[0.2em] uppercase px-8 flex items-center gap-2">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    ลด 10% สำหรับสมาชิกใหม่
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

    <!-- Scroll Reveal + Parallax -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll Reveal
            const reveals = document.querySelectorAll('[data-reveal]');
            if (reveals.length) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
                reveals.forEach(el => observer.observe(el));
            }

            // Parallax hero image
            const parallaxImg = document.querySelector('[data-parallax]');
            if (parallaxImg) {
                let ticking = false;
                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            const scrollY = window.scrollY;
                            const heroH = parallaxImg.closest('section').offsetHeight;
                            if (scrollY < heroH) {
                                parallaxImg.style.transform = `scale(1.05) translateY(${scrollY * 0.15}px)`;
                            }
                            ticking = false;
                        });
                        ticking = true;
                    }
                });
            }
        });
    </script>

</body>
</html>
