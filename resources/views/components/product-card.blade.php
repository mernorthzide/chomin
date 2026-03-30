@props(['product'])

<a href="{{ route('products.show', $product->slug) }}"
   class="group block">
    <!-- Image Container -->
    <div class="relative aspect-[3/4] overflow-hidden bg-brand-gray">
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
            <div class="w-full h-full flex items-center justify-center bg-brand-gray">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-brand-gray-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        <!-- Out of Stock Overlay -->
        @if(isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') === 0)
            <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                <span class="text-xs font-medium tracking-wider text-brand-gray-dark uppercase">หมดสต็อก</span>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="mt-3 space-y-1">
        <h3 class="text-sm font-medium text-brand-black truncate group-hover:text-brand-brown transition-colors duration-200">
            {{ $product->name }}
        </h3>
        <p class="text-sm text-brand-gray-medium">
            ฿{{ number_format($product->price, 0) }}
        </p>
    </div>
</a>
