@props([
    'title' => 'CHOMIN',
    'description' => '',
    'image' => '',
    'ogImage' => '',
    'type' => 'website',
    'noindex' => false,
    'jsonLd' => [],
])

@php
    $resolvedTitle = trim((string) $title);
    $hasBrand = preg_match('/CHO\.?MIN/i', $resolvedTitle) === 1;
    if (! $hasBrand) {
        $resolvedTitle = ($resolvedTitle !== '' ? $resolvedTitle.' | ' : '').'CHOMIN';
    }
    $resolvedDescription = $description ?: 'CHO.MIN — เชิ้ตดีไซน์ 50+ สี ไซส์ XS-6XL ออกแบบให้คุณเลือกได้ทุกดีเทล';
    $resolvedImage = $ogImage ?: ($image ?: url('/images/og-default.jpg'));
    $canonical = \App\Support\Seo::canonical();
    $alternates = \App\Support\Seo::alternates();
    $locale = app()->getLocale();
    $ogLocale = $locale === 'th' ? 'th_TH' : 'en_US';
@endphp

<title>{{ $resolvedTitle }}</title>
<meta name="description" content="{{ $resolvedDescription }}">

<link rel="canonical" href="{{ $canonical }}">
@foreach($alternates as $alt)
<link rel="alternate" hreflang="{{ $alt['hreflang'] }}" href="{{ $alt['href'] }}">
@endforeach

@if($noindex)
<meta name="robots" content="noindex, nofollow">
@else
<meta name="robots" content="index, follow, max-image-preview:large">
@endif

<meta property="og:site_name" content="CHOMIN">
<meta property="og:type" content="{{ $type }}">
<meta property="og:locale" content="{{ $ogLocale }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
<meta property="og:description" content="{{ $resolvedDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $resolvedImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $resolvedTitle }}">
<meta name="twitter:description" content="{{ $resolvedDescription }}">
<meta name="twitter:image" content="{{ $resolvedImage }}">

<meta name="theme-color" content="#000000">

{{-- Site-wide JSON-LD --}}
<script type="application/ld+json">{!! json_encode(\App\Support\Seo::organizationJsonLd(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode(\App\Support\Seo::websiteJsonLd(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- Page-specific JSON-LD --}}
@foreach((array) $jsonLd as $schema)
    @if(! empty($schema))
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endforeach
