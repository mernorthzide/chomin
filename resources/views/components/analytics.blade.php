@php
    $analyticsKeys = ['analytics_ga4_id', 'analytics_meta_pixel_id', 'analytics_tiktok_pixel_id', 'analytics_gtm_id'];
    $analyticsValues = \Illuminate\Support\Facades\Cache::rememberForever('site_analytics_ids', fn () =>
        \App\Models\SiteSetting::whereIn('key', $analyticsKeys)->pluck('value', 'key')->all()
    );
    $ga4 = $analyticsValues['analytics_ga4_id'] ?? null;
    $metaPixel = $analyticsValues['analytics_meta_pixel_id'] ?? null;
    $tiktokPixel = $analyticsValues['analytics_tiktok_pixel_id'] ?? null;
    $gtmId = $analyticsValues['analytics_gtm_id'] ?? null;
    $hasAnyTracker = filled($ga4) || filled($metaPixel) || filled($tiktokPixel) || filled($gtmId);
@endphp

@if($hasAnyTracker)
{{-- Google Consent Mode v2 — default everything to denied until user opts in --}}
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'granted',
    'security_storage': 'granted',
    'wait_for_update': 500,
});
(function syncConsentFromLocalStorage() {
    try {
        const raw = localStorage.getItem('chomin_cookie_consent');
        if (!raw) return;
        const c = JSON.parse(raw);
        gtag('consent', 'update', {
            'analytics_storage': c.analytics ? 'granted' : 'denied',
            'ad_storage': c.marketing ? 'granted' : 'denied',
            'ad_user_data': c.marketing ? 'granted' : 'denied',
            'ad_personalization': c.marketing ? 'granted' : 'denied',
        });
        window.__chominConsent = c;
    } catch (e) {}
})();
window.addEventListener('chomin:consent-updated', (event) => {
    const c = event.detail || {};
    gtag('consent', 'update', {
        'analytics_storage': c.analytics ? 'granted' : 'denied',
        'ad_storage': c.marketing ? 'granted' : 'denied',
        'ad_user_data': c.marketing ? 'granted' : 'denied',
        'ad_personalization': c.marketing ? 'granted' : 'denied',
    });
    window.__chominConsent = c;
});
</script>

@if($gtmId)
{{-- Google Tag Manager --}}
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@endif

@if($ga4)
{{-- Google Analytics 4 --}}
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
<script>
gtag('js', new Date());
gtag('config', '{{ $ga4 }}', { 'anonymize_ip': true });
</script>
@endif

@if($metaPixel)
{{-- Meta Pixel (gated by marketing consent) --}}
<script>
(function() {
    function loadMetaPixel() {
        if (window.__metaPixelLoaded) return;
        window.__metaPixelLoaded = true;
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
        (window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $metaPixel }}');
        fbq('track', 'PageView');
    }
    const c = window.__chominConsent;
    if (c && c.marketing) loadMetaPixel();
    window.addEventListener('chomin:consent-updated', (e) => {
        if (e.detail && e.detail.marketing) loadMetaPixel();
    });
})();
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixel }}&ev=PageView&noscript=1"/></noscript>
@endif

@if($tiktokPixel)
{{-- TikTok Pixel (gated by marketing consent) --}}
<script>
(function() {
    function loadTikTokPixel() {
        if (window.__ttqLoaded) return;
        window.__ttqLoaded = true;
        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
            ttq.load('{{ $tiktokPixel }}');
            ttq.page();
        }(window, document, 'ttq');
    }
    const c = window.__chominConsent;
    if (c && c.marketing) loadTikTokPixel();
    window.addEventListener('chomin:consent-updated', (e) => {
        if (e.detail && e.detail.marketing) loadTikTokPixel();
    });
})();
</script>
@endif
@endif
