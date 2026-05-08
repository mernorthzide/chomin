<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'CHOMIN') }}</title>
    <meta name="description" content="{{ $description ?? 'CHO.MIN — เชิ้ตดีไซน์ 50+ สี ไซส์ XS–6XL จัดส่งฟรีทั่วประเทศ' }}">
    <meta property="og:title" content="{{ $title ?? config('app.name', 'CHOMIN') }}">
    <meta property="og:description" content="{{ $description ?? 'CHO.MIN — เชิ้ตดีไซน์ 50+ สี ไซส์ XS–6XL จัดส่งฟรีทั่วประเทศ' }}">
    <meta property="og:type" content="website">
    @php
        $segments = request()->segments();
        $currentLocale = in_array($segments[0] ?? null, config('chomin.locales.supported', ['th', 'en']), true)
            ? array_shift($segments)
            : app()->getLocale();
        $localizedPath = implode('/', $segments);
    @endphp
    @foreach(config('chomin.locales.supported', ['th', 'en']) as $alternateLocale)
        <link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ url($alternateLocale.($localizedPath ? '/'.$localizedPath : '')) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url(config('chomin.locales.default', 'th').($localizedPath ? '/'.$localizedPath : '')) }}">
    @if(isset($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-brand-black">

    <!-- Announcement Ticker -->
    <div class="bg-brand-black text-white overflow-hidden">
        <div class="ticker-track flex whitespace-nowrap py-2.5">
            @for($i = 0; $i < 3; $i++)
                <span class="ticker-item text-[11px] tracking-[0.1em] uppercase px-10">
                    จัดส่งฟรีทั่วประเทศ
                </span>
                <span class="text-white/30 text-[11px]">&mdash;</span>
                <span class="ticker-item text-[11px] tracking-[0.1em] uppercase px-10">
                    เปลี่ยนคืนภายใน 30 วัน
                </span>
                <span class="text-white/30 text-[11px]">&mdash;</span>
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
    <x-live-chat-entry />
    <x-cookie-consent />

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
