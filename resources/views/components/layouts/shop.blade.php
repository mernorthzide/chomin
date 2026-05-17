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

    <!-- Announcement Ticker -->
    <div class="bg-brand-black text-white overflow-hidden border-b border-brand-black">
        <div class="ticker-track flex whitespace-nowrap py-2">
            @for($i = 0; $i < 3; $i++)
                <span class="ticker-item text-[11px] tracking-[0.14em] uppercase px-10">Free shipping Thailand</span>
                <span class="text-white/30 text-[11px]">/</span>
                <span class="ticker-item text-[11px] tracking-[0.14em] uppercase px-10">50+ colors</span>
                <span class="text-white/30 text-[11px]">/</span>
                <span class="ticker-item text-[11px] tracking-[0.14em] uppercase px-10">XS-6XL</span>
                <span class="text-white/30 text-[11px]">/</span>
                <span class="ticker-item text-[11px] tracking-[0.14em] uppercase px-10">30 day exchange</span>
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
