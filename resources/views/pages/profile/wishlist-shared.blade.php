@php $isEn = app()->getLocale() === 'en'; @endphp

<x-layouts.shop
    :title="($isEn ? $ownerName.\"'s Wishlist\" : 'Wishlist ของ '.$ownerName).' | CHOMIN'"
    :description="$isEn ? \"See what's on \".$ownerName.\"'s CHOMIN wishlist.\" : 'ดูสินค้าใน Wishlist ของ '.$ownerName"
    :noindex="true">

    <section class="border-b border-brand-gray-border bg-white">
        <div class="px-6 md:px-12 py-10 md:py-16 text-center">
            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Shared wishlist' : 'Wishlist ที่แชร์มา' }}</p>
            <h1 class="mt-3 font-serif text-3xl md:text-5xl uppercase">
                {{ $isEn ? $ownerName."'s picks" : 'รายการของ '.$ownerName }}
            </h1>
            <p class="mt-3 text-sm text-brand-gray-medium">
                {{ $wishlists->count() }} {{ $isEn ? 'items' : 'รายการ' }}
            </p>
        </div>
    </section>

    @if($wishlists->isEmpty())
        <section class="bg-white py-20 text-center">
            <p class="text-sm text-brand-gray-medium">
                {{ $isEn ? 'This wishlist is empty.' : 'Wishlist นี้ว่างเปล่า' }}
            </p>
            <a href="{{ route('shop.index') }}" class="mt-6 inline-block text-xs uppercase tracking-[0.16em] border-b border-brand-black pb-1">
                {{ $isEn ? 'Browse the shop' : 'ไปช้อปต่อ' }}
            </a>
        </section>
    @else
        <section class="bg-white">
            <div class="commerce-grid">
                @foreach($wishlists as $item)
                    @if($item->product)
                        <x-product-card :product="$item->product" />
                    @endif
                @endforeach
            </div>
        </section>
    @endif

</x-layouts.shop>
