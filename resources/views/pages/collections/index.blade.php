<x-layouts.shop>

    <!-- Page Header -->
    <section class="bg-brand-gray py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-serif text-4xl md:text-5xl font-normal text-brand-black uppercase tracking-widest">
                คอลเล็คชัน
            </h1>
            <p class="mt-4 text-sm text-brand-gray-medium tracking-wide">
                ค้นพบคอลเล็คชันพิเศษจาก CHOMIN
            </p>
        </div>
    </section>

    <!-- Collections Grid -->
    <section class="bg-white py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse($collections as $collection)
                @if($loop->first)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @endif

                <a href="{{ route('collections.show', $collection->slug) }}"
                   class="group relative aspect-[3/4] overflow-hidden bg-brand-gray block">

                    <!-- Collection Image -->
                    @if($collection->banner_image)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($collection->banner_image) }}"
                            alt="{{ $collection->name }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            loading="lazy">
                    @elseif($collection->image)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($collection->image) }}"
                            alt="{{ $collection->name }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            loading="lazy">
                    @else
                        <div class="absolute inset-0 bg-brand-gray flex items-center justify-center">
                            <span class="font-serif text-6xl text-brand-gray-border">{{ strtoupper(substr($collection->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    <!-- Dark Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent transition-opacity duration-300 group-hover:from-black/80"></div>

                    <!-- Text Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <h2 class="font-serif text-xl md:text-2xl font-normal text-white uppercase tracking-widest">
                            {{ $collection->name }}
                        </h2>
                        @if($collection->products_count > 0)
                            <p class="text-xs text-white/70 mt-1 tracking-wider">
                                {{ $collection->products_count }} รายการ
                            </p>
                        @endif
                        <div class="mt-3 flex items-center space-x-2 text-white/0 group-hover:text-white/80 transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                            <span class="text-xs tracking-[0.15em] uppercase">ชมคอลเล็คชัน</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </div>
                </a>

                @if($loop->last)
                    </div>
                @endif
            @empty
                <div class="text-center py-20">
                    <p class="text-brand-gray-medium text-sm">ยังไม่มีคอลเล็คชัน</p>
                </div>
            @endforelse
        </div>
    </section>

</x-layouts.shop>
