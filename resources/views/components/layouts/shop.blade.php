<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-meta
        :title="$title ?? 'CHOMIN'"
        :description="$description ?? ''"
        :image="$image ?? ''"
        :ogImage="$ogImage ?? ''"
        :type="$ogType ?? 'website'"
        :noindex="$noindex ?? false"
        :jsonLd="$jsonLd ?? []"
    />

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-analytics />
</head>
<body class="storefront font-sans antialiased bg-white text-brand-black">

    <!-- Announcement Bar -->
    <div class="bg-brand-black text-white border-b border-brand-black">
        <p class="px-4 py-2 text-center text-[11px] tracking-[0.14em] uppercase truncate">
            Free shipping Thailand <span class="text-white/30 px-2">/</span> S&ndash;XL <span class="text-white/30 px-2">/</span> 7 day exchange
        </p>
    </div>

    <!-- Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    <x-flash-toast />
    <x-newsletter-popup />
    <x-line-widget />
    <x-cookie-consent />
    <x-quick-view-modal />

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // ═══════════════════════════════════
        // SCROLL REVEAL
        // ═══════════════════════════════════
        const reveals = document.querySelectorAll('[data-reveal]');
        if (reveals.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            reveals.forEach(el => observer.observe(el));
        }

        // ═══════════════════════════════════
        // DRAG TO SCROLL (horizontal gallery)
        // ═══════════════════════════════════
        document.querySelectorAll('[data-drag-scroll]').forEach(el => {
            let isDown = false, startX, scrollLeft;

            el.addEventListener('mousedown', (e) => {
                isDown = true;
                el.classList.add('active');
                startX = e.pageX - el.offsetLeft;
                scrollLeft = el.scrollLeft;
            });
            el.addEventListener('mouseleave', () => { isDown = false; el.classList.remove('active'); });
            el.addEventListener('mouseup', () => { isDown = false; el.classList.remove('active'); });
            el.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - el.offsetLeft;
                const walk = (x - startX) * 1.5;
                el.scrollLeft = scrollLeft - walk;
            });

            // Prevent click after drag
            let dragDistance = 0;
            el.addEventListener('mousedown', (e) => { dragDistance = 0; });
            el.addEventListener('mousemove', () => { if (isDown) dragDistance++; });
            el.addEventListener('click', (e) => {
                if (dragDistance > 5) e.preventDefault();
            }, true);
        });
    });
    </script>

</body>
</html>
