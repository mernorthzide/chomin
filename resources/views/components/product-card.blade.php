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

<a href="{{ route('products.show', $product->slug) }}"
   class="product-card group focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-inset {{ $dark ? 'product-card-dark' : '' }}">
    <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray">
        @if($primaryImage)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($primaryImage->image_path) }}"
                 alt="{{ $product->localized_name }}"
                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                 loading="lazy">
        @else
            <div class="h-full w-full flex items-center justify-center">
                <span class="font-serif text-5xl text-brand-gray-border">CHO</span>
            </div>
        @endif

        @if(!empty($badges))
            <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                @foreach($badges as $badge)
                    <span class="bg-white px-2 py-1 text-[10px] uppercase tracking-[0.12em] text-brand-black">
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

    <div class="min-h-[118px] border-t border-brand-gray-border p-3 md:p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h3 class="text-xs uppercase tracking-[0.08em] leading-snug text-brand-black">
                    {{ $product->localized_name }}
                </h3>
                @if($product->collection ?? false)
                    <p class="mt-1 text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">
                        {{ $product->collection->localized_name ?? $product->collection->name }}
                    </p>
                @endif
            </div>
            <div class="text-right text-xs text-brand-black whitespace-nowrap">
                <span>฿{{ number_format($product->display_price, 0) }}</span>
                @if($product->is_on_sale)
                    <span class="mt-1 block text-[10px] text-brand-gray-light line-through">฿{{ number_format((float) $product->price, 0) }}</span>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            @if($colorPreview->isNotEmpty())
                <div class="flex -space-x-1">
                    @foreach($colorPreview->take(6) as $color)
                        <span class="h-4 w-4 rounded-full border border-white ring-1 ring-brand-gray-border"
                              style="background-color: {{ $color->color_code ?? '#eeeeee' }}"></span>
                    @endforeach
                </div>
                <span class="text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">
                    {{ $colorPreview->unique(fn ($color) => $color->slug ?: $color->name)->count() }} สี
                </span>
            @else
                <span class="text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">CHOMIN</span>
            @endif
        </div>
    </div>
</a>
