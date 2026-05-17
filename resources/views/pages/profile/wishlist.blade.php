<x-layouts.shop>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            บัญชีของฉัน
        </h1>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Sidebar --}}
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3">

                @if(session('success'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white border border-brand-gray-border p-6 md:p-8">

                    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black">Wishlist</h2>

                        @if(isset($shareUrl) && $wishlists->isNotEmpty())
                            <div x-data="{ copied: false, copy() { navigator.clipboard.writeText('{{ $shareUrl }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }"
                                 class="flex items-center gap-2">
                                <button type="button" @click="copy()"
                                        class="inline-flex items-center gap-2 border border-brand-black px-3 py-2 text-[11px] uppercase tracking-[0.14em] hover:bg-brand-black hover:text-white">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                    </svg>
                                    <span x-text="copied ? '{{ app()->getLocale() === 'en' ? 'Copied' : 'คัดลอกแล้ว' }}' : '{{ app()->getLocale() === 'en' ? 'Share wishlist' : 'แชร์ Wishlist' }}'"></span>
                                </button>
                                <form method="POST" action="{{ route('wishlist.share-token.regenerate') }}">
                                    @csrf
                                    <button type="submit" class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light underline">
                                        {{ app()->getLocale() === 'en' ? 'New link' : 'สร้างลิงก์ใหม่' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($wishlists->isEmpty())
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-brand-gray-border mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <p class="text-brand-gray-medium text-sm mb-4">Wishlist ว่างเปล่า</p>
                            <a href="{{ route('shop.index') }}"
                               class="inline-block px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-gray-dark transition-colors duration-300">
                                เลือกซื้อสินค้า
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($wishlists as $item)
                                <div class="group relative">
                                    {{-- Remove Button --}}
                                    <form method="POST" action="{{ route('wishlist.toggle') }}" class="absolute top-2 right-2 z-10">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <button type="submit"
                                                title="นำออกจาก Wishlist"
                                                class="w-7 h-7 flex items-center justify-center bg-white bg-opacity-90 text-red-400 hover:text-red-600 border border-brand-gray-border shadow-sm transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Product Card --}}
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="block">
                                            <div class="aspect-[3/4] bg-brand-gray overflow-hidden mb-2">
                                                @if($item->product->primaryImage)
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->primaryImage->image_path) }}"
                                                         alt="{{ $item->product->name }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                @endif
                                            </div>
                                            <p class="text-xs text-brand-black font-medium truncate">{{ $item->product->name }}</p>
                                            <p class="text-xs text-brand-gray-medium mt-0.5">฿{{ number_format($item->product->price, 0) }}</p>
                                        </a>
                                    @else
                                        <div class="aspect-[3/4] bg-brand-gray flex items-center justify-center mb-2">
                                            <p class="text-xs text-brand-gray-medium">สินค้าไม่พบ</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
