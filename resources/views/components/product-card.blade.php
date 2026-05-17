@props(['product', 'dark' => false])

@php
    $badges = [];
    if ($product->created_at && $product->created_at->gt(now()->subDays(14))) {
        $badges[] = 'ใหม่';
    }
    if ($product->is_on_sale) {
        $badges[] = 'Sale';
    }
    if (isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') <= 5 && $product->variants->sum('stock') > 0) {
        $badges[] = 'เหลือน้อย';
    }

    $primaryImage = $product->primaryImage ?? $product->images->first();
    $colorPreview = $product->colors ?? collect();
@endphp

<div class="product-card-wrapper group relative {{ $dark ? 'product-card-dark' : '' }}">
<a href="{{ route('products.show', $product->slug) }}"
   class="product-card block focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-inset">
    <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray">
        @if($primaryImage)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($primaryImage->image_path) }}"
                 alt="{{ $product->localized_name }}"
                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                 loading="lazy"
                 decoding="async"
                 sizes="(max-width: 768px) 50vw, (max-width: 1024px) 33vw, 25vw">
        @else
            <div class="h-full w-full flex items-center justify-center">
                <span class="font-serif text-5xl text-brand-gray-border">CHO</span>
            </div>
        @endif

        @if(!empty($badges))
            <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                @foreach($badges as $badge)
                    <span class="bg-white px-2.5 py-1.5 text-[10px] uppercase tracking-[0.08em] text-brand-black">
                        {{ $badge }}
                    </span>
                @endforeach
            </div>
        @endif

        @if(isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') === 0)
            <div class="absolute inset-0 flex items-center justify-center bg-white/70">
                <span class="text-xs uppercase tracking-[0.2em] text-brand-black">หมดสต็อก</span>
            </div>
        @endif
    </div>

    <div class="min-h-[146px] border-t border-brand-gray-border p-4 md:p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h3 class="text-xs uppercase tracking-[0.08em] leading-snug text-brand-black">
                    {{ $product->localized_name }}
                </h3>
                @if($product->collection ?? false)
                    <p class="mt-1.5 text-[10px] uppercase tracking-[0.08em] text-brand-gray-light">
                        {{ $product->collection->localized_name ?? $product->collection->name }}
                    </p>
                @endif
            </div>
            <div class="text-right text-xs text-brand-black whitespace-nowrap">
                <span>฿{{ number_format($product->display_price, 0) }}</span>
                @if($product->is_on_sale)
                    <span class="mt-1.5 block text-[10px] text-brand-gray-light line-through">฿{{ number_format((float) $product->price, 0) }}</span>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            @if($colorPreview->isNotEmpty())
                <div class="flex -space-x-1">
                    @foreach($colorPreview->take(6) as $color)
                        <span class="h-5 w-5 rounded-full border border-white ring-1 ring-brand-gray-border"
                              style="background-color: {{ $color->color_code ?? '#eeeeee' }}"></span>
                    @endforeach
                </div>
                <span class="text-[10px] uppercase tracking-[0.08em] text-brand-gray-light">
                    {{ $colorPreview->unique(fn ($color) => $color->slug ?: $color->name)->count() }} สี
                </span>
            @else
                <span class="text-[10px] uppercase tracking-[0.08em] text-brand-gray-light">CHOMIN</span>
            @endif
        </div>
    </div>
</a>
<button type="button"
        @click.prevent="$dispatch('open-quick-view', { slug: '{{ $product->slug }}' })"
        class="absolute left-1/2 top-[36%] z-10 -translate-x-1/2 -translate-y-1/2 opacity-0 transition-opacity group-hover:opacity-100 focus:opacity-100 bg-white px-5 py-2.5 text-[11px] uppercase tracking-[0.14em] text-brand-black shadow-sm hover:bg-brand-black hover:text-white"
        aria-label="Quick view {{ $product->localized_name }}">
    {{ app()->getLocale() === 'en' ? 'Quick View' : 'ดูเร็ว' }}
</button>
</div>
