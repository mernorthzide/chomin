@props(['limit' => 8])

@php
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Http;

    $handle = config('chomin.social.instagram_handle', 'chomin.th');
    $url = config('chomin.social.instagram_url', 'https://www.instagram.com/chomin.th/');
    $token = config('chomin.social.instagram_token');
    $isEn = app()->getLocale() === 'en';

    $posts = Cache::remember("instagram-feed:{$handle}:{$limit}", 3600, function () use ($token, $limit) {
        if (! $token) {
            return [];
        }
        try {
            $res = Http::timeout(4)->get('https://graph.instagram.com/me/media', [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink',
                'access_token' => $token,
                'limit' => $limit,
            ]);
            if (! $res->ok()) return [];

            return collect($res->json('data', []))
                ->filter(fn ($p) => in_array($p['media_type'] ?? '', ['IMAGE', 'CAROUSEL_ALBUM', 'VIDEO']))
                ->map(fn ($p) => [
                    'image' => $p['media_type'] === 'VIDEO' ? ($p['thumbnail_url'] ?? $p['media_url']) : $p['media_url'],
                    'permalink' => $p['permalink'],
                    'caption' => $p['caption'] ?? '',
                ])
                ->take($limit)
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    });

    // Fallback: pull approved review photos as community gallery so the section is never empty
    if (empty($posts)) {
        $reviewPhotos = \App\Models\ProductReview::approved()
            ->whereNotNull('photos')
            ->latest('approved_at')
            ->limit($limit)
            ->get()
            ->flatMap(fn ($r) => collect($r->photos ?? [])->map(fn ($p) => [
                'image' => \Illuminate\Support\Facades\Storage::url($p),
                'permalink' => $url,
                'caption' => $r->title ?? '',
            ]))
            ->take($limit)
            ->values()
            ->all();
        $posts = $reviewPhotos;
    }
@endphp

@if(!empty($posts))
    <section class="border-t border-brand-gray-border bg-white" aria-label="Instagram feed">
        <div class="px-6 md:px-12 py-10 md:py-14 flex items-end justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-2">
                    {{ $isEn ? 'On Instagram' : 'บน Instagram' }}
                </p>
                <h2 class="font-serif text-3xl md:text-5xl uppercase leading-none">@{{ $handle }}</h2>
            </div>
            <a href="{{ $url }}" target="_blank" rel="noopener"
               class="hidden sm:inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1 hover:opacity-60">
                {{ $isEn ? 'Follow' : 'ติดตาม' }}
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-brand-gray-border">
            @foreach($posts as $post)
                <a href="{{ $post['permalink'] }}" target="_blank" rel="noopener"
                   class="block aspect-square bg-brand-gray overflow-hidden group relative">
                    <img src="{{ $post['image'] }}" alt="Instagram post" loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <span class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition-colors">
                        <svg class="h-6 w-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="5"/>
                            <circle cx="12" cy="12" r="4"/>
                            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif
