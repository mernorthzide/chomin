<x-layouts.shop>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Success Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-2">
                สั่งซื้อสำเร็จ
            </h1>
            <p class="text-sm text-brand-gray-medium">
                หมายเลขคำสั่งซื้อ:
                <span class="font-medium text-brand-black">{{ $order->order_number }}</span>
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            {{-- Left: Payment Info --}}
            <div class="space-y-6">

                {{-- Order Status --}}
                <div class="border border-brand-gray-border p-5">
                    <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-4">
                        สถานะคำสั่งซื้อ
                    </h2>
                    <div class="flex items-center gap-3">
                        @php
                            $statusColor = match($order->status) {
                                'pending' => 'text-amber-600 bg-amber-50 border-amber-200',
                                'awaiting_payment' => 'text-blue-600 bg-blue-50 border-blue-200',
                                'paid' => 'text-green-600 bg-green-50 border-green-200',
                                default => 'text-brand-gray-dark bg-brand-gray border-brand-gray-border',
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 text-xs font-medium border {{ $statusColor }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="border border-brand-gray-border p-5">
                    <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-4">
                        รายการสินค้า
                    </h2>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <div class="min-w-0 pr-4">
                                    <p class="text-brand-black truncate">{{ $item->product_name }}</p>
                                    <p class="text-xs text-brand-gray-medium mt-0.5">
                                        {{ $item->color_name }} / {{ $item->size }} x{{ $item->quantity }}
                                    </p>
                                </div>
                                <span class="text-brand-black flex-shrink-0">฿{{ number_format($item->price * $item->quantity, 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-brand-gray-border mt-4 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-brand-gray-medium">
                            <span>ยอดรวมสินค้า</span>
                            <span>฿{{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-brand-gray-medium">
                            <span>ค่าจัดส่ง</span>
                            <span>฿{{ number_format($order->shipping_fee, 0) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>ส่วนลด</span>
                                <span>-฿{{ number_format($order->discount, 0) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-medium text-brand-black border-t border-brand-gray-border pt-2">
                            <span>ยอดที่ต้องชำระ</span>
                            <span class="text-lg">฿{{ number_format($order->total, 0) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="border border-brand-gray-border p-5">
                    <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-4">
                        ที่อยู่จัดส่ง
                    </h2>
                    <div class="text-sm text-brand-gray-medium space-y-1">
                        <p class="text-brand-black font-medium">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_district }} {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                        @if($order->note)
                            <p class="mt-2 pt-2 border-t border-brand-gray-border text-xs italic">หมายเหตุ: {{ $order->note }}</p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Right: PromptPay + Slip Upload --}}
            <div class="space-y-6">

                {{-- PromptPay Info --}}
                @if($promptpay['id'] || $promptpay['qr'])
                    <div class="border border-brand-gray-border p-5">
                        <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-4">
                            ชำระเงินผ่าน PromptPay
                        </h2>

                        @if($promptpay['qr'])
                            <div class="flex justify-center mb-4">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($promptpay['qr']) }}"
                                     alt="QR Code PromptPay"
                                     class="w-48 h-48 object-contain border border-brand-gray-border p-2">
                            </div>
                        @endif

                        <div class="text-center space-y-1">
                            @if($promptpay['name'])
                                <p class="text-sm font-medium text-brand-black">{{ $promptpay['name'] }}</p>
                            @endif
                            @if($promptpay['id'])
                                <p class="text-sm text-brand-gray-medium">{{ $promptpay['id'] }}</p>
                            @endif
                            <div class="mt-3 pt-3 border-t border-brand-gray-border">
                                <p class="text-xs text-brand-gray-medium">ยอดที่ต้องโอน</p>
                                <p class="text-2xl font-medium text-brand-black mt-1">฿{{ number_format($order->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border border-brand-gray-border p-5">
                        <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                            ข้อมูลการชำระเงิน
                        </h2>
                        <p class="text-sm text-brand-gray-medium">กรุณาติดต่อร้านค้าเพื่อรับข้อมูลการชำระเงิน</p>
                    </div>
                @endif

                {{-- Slip Upload --}}
                <div class="border border-brand-gray-border p-5">
                    <h2 class="text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-4">
                        แนบสลิปการโอนเงิน
                    </h2>

                    @if(session('success') && str_contains(session('success'), 'สลิป'))
                        <div class="mb-4 px-3 py-2 bg-green-50 border border-green-200 text-green-700 text-xs">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($order->paymentSlip)
                        <div class="mb-4">
                            <p class="text-xs text-brand-gray-medium mb-2">สลิปที่อัปโหลดล่าสุด:</p>
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($order->paymentSlip->image_path) }}"
                                 alt="สลิปการโอนเงิน"
                                 class="w-full max-w-xs border border-brand-gray-border object-contain">

                            @if($order->paymentSlip->isConfirmed())
                                <p class="mt-2 text-xs text-green-600 font-medium">ยืนยันแล้ว</p>
                            @elseif($order->paymentSlip->isRejected())
                                <p class="mt-2 text-xs text-red-500">ถูกปฏิเสธ: {{ $order->paymentSlip->rejection_reason }}</p>
                            @else
                                <p class="mt-2 text-xs text-amber-600">รอตรวจสอบ...</p>
                            @endif
                        </div>
                    @endif

                    @if(in_array($order->status, ['pending', 'awaiting_payment']))
                        <form action="{{ route('orders.slip.store', $order) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @error('slip')
                                <p class="mb-2 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <div x-data="{ fileName: '' }">
                                <label class="block w-full cursor-pointer">
                                    <div class="border-2 border-dashed border-brand-gray-border hover:border-brand-gray-dark transition-colors duration-200 p-6 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-brand-gray-border mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <p class="text-xs text-brand-gray-medium" x-text="fileName || 'คลิกเพื่อเลือกไฟล์สลิป'"></p>
                                        <p class="text-xs text-brand-gray-border mt-1">PNG, JPG ไม่เกิน 5MB</p>
                                    </div>
                                    <input type="file" name="slip" accept="image/*" class="sr-only"
                                           @change="fileName = $event.target.files[0]?.name || ''">
                                </label>
                            </div>

                            <button type="submit"
                                    class="mt-4 w-full py-3 text-sm font-medium tracking-[0.15em] uppercase bg-brand-black text-white hover:bg-brand-brown transition-colors duration-300">
                                อัปโหลดสลิป
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <a href="{{ route('home') }}"
                       class="flex-1 py-3 text-center text-xs font-medium tracking-widest uppercase border border-brand-gray-border text-brand-gray-dark hover:border-brand-black hover:text-brand-black transition-colors duration-200">
                        กลับหน้าแรก
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="flex-1 py-3 text-center text-xs font-medium tracking-widest uppercase bg-brand-black text-white hover:bg-brand-brown transition-colors duration-200">
                        ช้อปต่อ
                    </a>
                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
