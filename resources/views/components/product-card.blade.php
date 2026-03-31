@props(['product', 'dark' => false])

<a href="{{ route('products.show', $product->slug) }}"
   class="group block">
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

        <!-- Out of Stock Overlay -->
        @if(isset($product->variants) && $product->variants->isNotEmpty() && $product->variants->sum('stock') === 0)
            <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                <span class="text-[10px] font-bold tracking-[0.2em] text-brand-gray-dark uppercase">หมดสต็อก</span>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="mt-4">
        <h3 class="text-[11px] font-bold uppercase tracking-widest mb-1 truncate transition-colors duration-200 {{ $dark ? 'text-white group-hover:text-brand-gray-light' : 'text-brand-black group-hover:text-brand-brown' }}">
            {{ $product->name }}
        </h3>
        <p class="text-[11px] uppercase {{ $dark ? 'text-brand-gray-light' : 'text-brand-gray-medium' }}">
            ฿{{ number_format($product->price, 0) }}
        </p>
    </div>
</a>
