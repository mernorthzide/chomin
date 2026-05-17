<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-meta
        :title="$title ?? config('app.name', 'CHOMIN')"
        :description="$description ?? ''"
        :image="$image ?? ''"
        :ogImage="$ogImage ?? ''"
        :type="$ogType ?? 'website'"
        :jsonLd="$jsonLd ?? []"
    />

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-analytics />
</head>
<body class="font-sans antialiased bg-white text-brand-black" x-data="{ mobileMenu: false }">

    <!-- Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Flash Notifications -->
    <x-flash-toast />

    <!-- Cookie Consent -->
    <x-cookie-consent />

    <!-- Quick View Modal (lazy-loaded on demand) -->
    <x-quick-view-modal />

    <!-- Newsletter Popup (smart-timed, dismissible) -->
    <x-newsletter-popup />

    <!-- LINE chat / contact floating widget -->
    <x-line-widget />

</body>
</html>
