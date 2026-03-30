<x-layouts.shop>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        {{-- Page Title --}}
        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            ตะกร้าสินค้า
        </h1>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @if($cart->items->isEmpty())
            {{-- Empty Cart --}}
            <div class="text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-brand-gray-border mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-brand-gray-medium text-sm tracking-wide mb-6">ตะกร้าของคุณว่างเปล่า</p>
                <a href="{{ route('shop.index') }}"
                   class="inline-block px-8 py-3 bg-brand-black text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-brand-brown transition-colors duration-300">
                    เลือกซื้อสินค้า
                </a>
            </div>
        @else
            <div class="lg:grid lg:grid-cols-3 lg:gap-12">

                {{-- Cart Items --}}
                <div class="lg:col-span-2">
                    <div class="divide-y divide-brand-gray-border">
                        @foreach($cart->items as $item)
                            <div class="py-6 flex gap-4" x-data="{ qty: {{ $item->quantity }} }">

                                {{-- Product Image --}}
                                <div class="flex-shrink-0 w-24 h-32 md:w-28 md:h-36 overflow-hidden bg-brand-gray">
                                    @if($item->product->primaryImage)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->primaryImage->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-brand-gray-border" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div class="min-w-0 pr-4">
                                            <h3 class="text-sm font-medium text-brand-black truncate">
                                                {{ $item->product->name }}
                                            </h3>
                                            <p class="mt-1 text-xs text-brand-gray-medium">
                                                @if($item->variant->color)
                                                    {{ $item->variant->color->name }} /
                                                @endif
                                                {{ $item->variant->size }}
                                            </p>
                                            <p class="mt-1 text-sm text-brand-black">
                                                ฿{{ number_format($item->product->price, 0) }}
                                            </p>
                                        </div>

                                        {{-- Remove Button --}}
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-brand-gray-medium hover:text-red-500 transition-colors duration-200"
                                                    title="ลบสินค้า">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Quantity Controls --}}
                                    <div class="mt-4 flex items-center justify-between">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex items-center border border-brand-gray-border">
                                                <button type="button"
                                                        @click="qty = Math.max(1, qty - 1)"
                                                        class="w-8 h-8 flex items-center justify-center text-brand-gray-dark hover:bg-brand-gray transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <input type="number" name="quantity"
                                                       x-model="qty"
                                                       min="1"
                                                       max="{{ $item->variant->stock }}"
                                                       class="w-10 h-8 text-center border-x border-brand-gray-border text-sm text-brand-black focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                                <button type="button"
                                                        @click="qty = Math.min({{ $item->variant->stock }}, qty + 1)"
                                                        class="w-8 h-8 flex items-center justify-center text-brand-gray-dark hover:bg-brand-gray transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <button type="submit"
                                                    class="ml-3 text-xs text-brand-gray-medium hover:text-brand-black underline transition-colors duration-200">
                                                อัปเดต
                                            </button>
                                        </form>

                                        {{-- Line Total --}}
                                        <p class="text-sm font-medium text-brand-black">
                                            ฿{{ number_format($item->line_total, 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Continue Shopping --}}
                    <div class="mt-6">
                        <a href="{{ route('shop.index') }}"
                           class="text-xs text-brand-gray-medium hover:text-brand-black underline tracking-wide transition-colors duration-200">
                            &larr; เลือกซื้อสินค้าต่อ
                        </a>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="mt-10 lg:mt-0">
                    <div class="bg-brand-gray p-6 sticky top-4"
                         x-data="{
                             couponCode: '',
                             pointsUsed: 0,
                             maxPoints: {{ auth()->check() ? auth()->user()->points : 0 }},
                         }">

                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-6">
                            สรุปคำสั่งซื้อ
                        </h2>

                        {{-- Subtotal --}}
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-brand-gray-medium">ยอดรวมสินค้า</span>
                                <span class="text-brand-black">฿{{ number_format($cart->subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-brand-gray-medium">ค่าจัดส่ง</span>
                                <span class="text-brand-black text-xs italic">(คำนวณตอนชำระเงิน)</span>
                            </div>
                        </div>

                        <div class="border-t border-brand-gray-border my-5"></div>

                        {{-- Coupon Code --}}
                        <div class="mb-5">
                            <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                                รหัสคูปอง
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       x-model="couponCode"
                                       placeholder="ใส่รหัสคูปอง"
                                       class="flex-1 border border-brand-gray-border px-3 py-2 text-xs focus:outline-none focus:border-brand-black bg-white">
                            </div>
                        </div>

                        {{-- Points --}}
                        @auth
                        <div class="mb-5">
                            <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                                แต้มสะสม
                            </label>
                            <p class="text-xs text-brand-gray-medium mb-2">
                                แต้มของคุณ: <span class="text-brand-black font-medium">{{ number_format(auth()->user()->points) }}</span> แต้ม
                            </p>
                            <input type="number"
                                   x-model="pointsUsed"
                                   min="0"
                                   :max="maxPoints"
                                   placeholder="จำนวนแต้มที่ต้องการใช้"
                                   class="w-full border border-brand-gray-border px-3 py-2 text-xs focus:outline-none focus:border-brand-black bg-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                        </div>
                        @endauth

                        <div class="border-t border-brand-gray-border my-5"></div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-sm font-medium text-brand-black">ยอดรวม</span>
                            <span class="text-lg font-medium text-brand-black">฿{{ number_format($cart->subtotal, 0) }}</span>
                        </div>

                        {{-- Checkout Button --}}
                        @auth
                            <a :href="`{{ route('checkout.index') }}?coupon_code=${encodeURIComponent(couponCode)}&points_used=${pointsUsed}`"
                               class="block w-full py-4 text-center text-sm font-medium tracking-[0.15em] uppercase bg-brand-black text-white hover:bg-brand-brown transition-colors duration-300">
                                ดำเนินการสั่งซื้อ
                            </a>
                        @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(route('checkout.index')) }}"
                               class="block w-full py-4 text-center text-sm font-medium tracking-[0.15em] uppercase bg-brand-black text-white hover:bg-brand-brown transition-colors duration-300">
                                เข้าสู่ระบบเพื่อสั่งซื้อ
                            </a>
                        @endauth

                    </div>
                </div>
            </div>
        @endif

    </div>

</x-layouts.shop>
