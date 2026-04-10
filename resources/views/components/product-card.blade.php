@props(['product', 'dark' => false])

@php
    $badges = [];
    if ($product->created_at && $product->created_at->gt(now()->subDays(14))) {
        $badges[] = ['label' => 'ใหม่', 'class' => 'badge-new'];
    }
    if (isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') <= 5 && $product->variants->sum('stock') > 0) {
        $badges[] = ['label' => 'เหลือน้อย', 'class' => 'badge-sale'];
    }
@endphp

<a href="{{ route('products.show', $product->slug) }}"
   class="group block focus:outline-none focus:ring-2 focus:ring-brand-black focus:ring-offset-2">
    <!-- Image Container -->
    <div class="relative aspect-[3/4] overflow-hidden {{ $dark ? 'bg-brand-gray-dark' : 'bg-brand-gray' }}">
        @php
            $primaryImage = $product->primaryImage ?? $product->images->first();
        @endphp
        @if($primaryImage)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($primaryImage->image_path) }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 {{ $dark ? 'text-brand-gray-medium' : 'text-brand-gray-light' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        <!-- Badges -->
        @if(!empty($badges))
            <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                @foreach($badges as $badge)
                    <span class="{{ $badge['class'] }} text-[10px] font-bold tracking-wider uppercase px-2 py-0.5">
                        {{ $badge['label'] }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Out of Stock Overlay -->
        @if(isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') === 0)
            <div class="absolute inset-0 bg-white/60 flex items-center justify-center backdrop-blur-[1px]">
                <span class="text-xs font-bold tracking-[0.2em] text-brand-gray-dark uppercase">หมดสต็อก</span>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="mt-4">
        <h3 class="text-xs font-bold uppercase tracking-widest mb-1 truncate transition-colors duration-200 {{ $dark ? 'text-white group-hover:text-brand-gray-light' : 'text-brand-black group-hover:text-brand-gray-dark' }}">
            {{ $product->name }}
        </h3>
        <p class="text-xs uppercase {{ $dark ? 'text-brand-gray-light' : 'text-brand-gray-medium' }}">
            ฿{{ number_format($product->price, 0) }}
        </p>
        @if($product->variants && $product->variants->unique('color')->count() > 1)
            <p class="text-[10px] text-brand-gray-light uppercase tracking-wider mt-1">
                มี {{ $product->variants->unique('color')->count() }} สี
            </p>
        @endif
    </div>
</a>
