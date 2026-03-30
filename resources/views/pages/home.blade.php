<x-layouts.shop>
    {{-- ============================================================
         SECTION 1: HERO BANNER
    ============================================================ --}}
    @if($banners->isNotEmpty())
        @php $hero = $banners->first(); @endphp
        <section class="relative w-full aspect-[16/9] md:aspect-[21/9] overflow-hidden bg-brand-black">
            @if($hero->image)
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($hero->image) }}"
                    alt="{{ $hero->title }}"
                    class="absolute inset-0 w-full h-full object-cover opacity-80">
            @endif

            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/30 to-black/60"></div>

            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-6">
                @if($hero->title)
                    <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-normal text-white tracking-widest uppercase mb-4">
                        {{ $hero->title }}
                    </h1>
                @endif
                @if($hero->subtitle)
                    <p class="text-sm sm:text-base md:text-lg text-white/80 tracking-[0.15em] mb-8 max-w-xl">
                        {{ $hero->subtitle }}
                    </p>
                @endif
                @if($hero->link)
                    <a href="{{ $hero->link }}"
                       class="inline-block px-8 py-3 border border-white text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-white hover:text-brand-black transition-all duration-300">
                        ชมคอลเล็คชัน
                    </a>
                @else
                    <a href="{{ route('shop.index') }}"
                       class="inline-block px-8 py-3 border border-white text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-white hover:text-brand-black transition-all duration-300">
                        ชมคอลเล็คชัน
                    </a>
                @endif
            </div>

            <!-- Additional banner dots (if multiple) -->
            @if($banners->count() > 1)
                <div class="absolute bottom-6 left-0 right-0 flex justify-center space-x-2 z-10">
                    @foreach($banners as $i => $banner)
                        <span class="w-1.5 h-1.5 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/40' }}"></span>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        {{-- Fallback hero when no banners --}}
        <section class="relative w-full aspect-[16/9] md:aspect-[21/9] bg-brand-black flex items-center justify-center overflow-hidden">
            <div class="text-center px-6">
                <x-brand-logo variant="white" class="h-16 md:h-24 mx-auto mb-4" />
                <p class="text-sm md:text-base text-white/70 tracking-[0.15em] mb-8">Thai Premium Fashion</p>
                <a href="{{ route('shop.index') }}"
                   class="inline-block px-8 py-3 border border-white text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-white hover:text-brand-black transition-all duration-300">
                    ชมสินค้าทั้งหมด
                </a>
            </div>
        </section>
    @endif

    {{-- ============================================================
         SECTION 2: COLLECTION SECTIONS
    ============================================================ --}}
    @forelse($collections as $index => $collection)
        @php
            // Alternate backgrounds: white → black → gray
            $bgVariants = ['bg-white', 'bg-brand-black', 'bg-brand-gray'];
            $bg = $bgVariants[$index % 3];
            $isDark = $bg === 'bg-brand-black';
            $textColor = $isDark ? 'text-white' : 'text-brand-black';
            $subTextColor = $isDark ? 'text-white/60' : 'text-brand-gray-medium';
            $linkColor = $isDark ? 'text-white border-white hover:bg-white hover:text-brand-black' : 'text-brand-black border-brand-black hover:bg-brand-black hover:text-white';
        @endphp

        <section class="{{ $bg }} py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Collection Header -->
                <div class="mb-10 md:mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div>
                        <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl font-normal {{ $textColor }} uppercase tracking-widest">
                            {{ $collection->name }}
                        </h2>
                        @if($collection->description)
                            <p class="mt-3 text-sm {{ $subTextColor }} max-w-xl leading-relaxed tracking-wide">
                                {{ $collection->description }}
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('collections.show', $collection->slug) }}"
                       class="inline-flex items-center space-x-2 text-xs font-medium tracking-[0.15em] uppercase border px-5 py-2.5 {{ $linkColor }} transition-all duration-300 flex-shrink-0">
                        <span>ดูทั้งหมด</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <!-- Products Horizontal Scroll Row -->
                @if($collection->products->isNotEmpty())
                    <div class="flex gap-4 md:gap-6 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 sm:overflow-visible">
                        @foreach($collection->products as $product)
                            <div class="flex-shrink-0 w-48 sm:w-auto snap-start {{ $isDark ? '[&_h3]:text-white [&_p]:text-white/60 [&_h3:hover]:text-brand-gray-light' : '' }}">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm {{ $subTextColor }}">ยังไม่มีสินค้าในคอลเล็คชันนี้</p>
                @endif
            </div>
        </section>

    @empty
        <section class="bg-brand-gray py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-brand-gray-medium">ยังไม่มีคอลเล็คชัน</p>
            </div>
        </section>
    @endforelse

    {{-- ============================================================
         SECTION 3: QUOTE
    ============================================================ --}}
    @if($quote)
        <section class="bg-white py-20 md:py-32">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <div class="w-8 h-px bg-brand-gray-border mx-auto mb-8"></div>
                <blockquote class="font-serif text-2xl md:text-3xl lg:text-4xl italic font-normal text-brand-black leading-relaxed">
                    {{ $quote }}
                </blockquote>
                <div class="w-8 h-px bg-brand-gray-border mx-auto mt-8"></div>
                <p class="mt-6 text-xs tracking-[0.25em] uppercase text-brand-gray-medium">CHOMIN</p>
            </div>
        </section>
    @endif

</x-layouts.shop>
