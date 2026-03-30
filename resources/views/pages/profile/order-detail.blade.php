<x-layouts.shop>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('orders.index') }}"
               class="text-xs text-brand-gray-medium hover:text-brand-black underline tracking-wide transition-colors duration-150">
                &larr; ประวัติสั่งซื้อ
            </a>
            <h1 class="text-xl md:text-2xl font-medium text-brand-black tracking-widest uppercase">
                ออเดอร์ {{ $order->order_number }}
            </h1>
        </div>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Sidebar --}}
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3 space-y-6">

                @if(session('success'))
                    <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Order Info --}}
                <div class="bg-white border border-brand-gray-border p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs text-brand-gray-medium tracking-wide">เลขออเดอร์</p>
                            <p class="text-lg font-medium text-brand-black">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-gray-medium tracking-wide">วันที่สั่ง</p>
                            <p class="text-sm text-brand-black">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'awaiting_payment' => 'bg-blue-100 text-blue-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'shipping' => 'bg-purple-100 text-purple-700',
                                    'completed' => 'bg-gray-100 text-gray-700',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 text-xs font-medium rounded {{ $statusColor }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>

                    {{-- Status Timeline --}}
                    @php
                        $steps = [
                            ['key' => 'pending', 'label' => 'รอชำระเงิน'],
                            ['key' => 'awaiting_payment', 'label' => 'รอตรวจสอบสลิป'],
                            ['key' => 'paid', 'label' => 'ชำระเงินแล้ว'],
                            ['key' => 'shipping', 'label' => 'กำลังจัดส่ง'],
                            ['key' => 'completed', 'label' => 'สำเร็จ'],
                        ];
                        $statusOrder = ['pending' => 0, 'awaiting_payment' => 1, 'paid' => 2, 'shipping' => 3, 'completed' => 4, 'cancelled' => -1];
                        $currentStep = $statusOrder[$order->status] ?? 0;
                    @endphp

                    @if($order->status !== 'cancelled')
                        <div class="mt-6 relative">
                            <div class="absolute top-4 left-0 right-0 h-0.5 bg-brand-gray-border" aria-hidden="true">
                                <div class="h-full bg-brand-black transition-all duration-500"
                                     style="width: {{ $currentStep > 0 ? ($currentStep / (count($steps) - 1)) * 100 : 0 }}%"></div>
                            </div>
                            <div class="relative flex justify-between">
                                @foreach($steps as $i => $step)
                                    @php $done = $i <= $currentStep; @endphp
                                    <div class="flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center z-10
                                            {{ $done ? 'bg-brand-black text-white' : 'bg-white border-2 border-brand-gray-border text-brand-gray-border' }}">
                                            @if($done)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <span class="text-xs">{{ $i + 1 }}</span>
                                            @endif
                                        </div>
                                        <p class="mt-2 text-xs text-center {{ $done ? 'text-brand-black font-medium' : 'text-brand-gray-medium' }} leading-tight">
                                            {{ $step['label'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-4 px-3 py-2 bg-red-50 border border-red-200 text-red-600 text-sm">
                            ออเดอร์นี้ถูกยกเลิกแล้ว
                        </div>
                    @endif
                </div>

                {{-- Tracking Info --}}
                @if(in_array($order->status, ['shipping', 'completed']) && $order->tracking_number)
                    <div class="bg-white border border-brand-gray-border p-6">
                        <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-4">
                            ข้อมูลการจัดส่ง
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-brand-gray-medium text-xs tracking-wide mb-1">บริษัทขนส่ง</p>
                                <p class="font-medium text-brand-black">{{ $order->carrier_name }}</p>
                            </div>
                            <div>
                                <p class="text-brand-gray-medium text-xs tracking-wide mb-1">เลขพัสดุ</p>
                                <p class="font-medium text-brand-black font-mono">{{ $order->tracking_number }}</p>
                            </div>
                            @if($order->shipped_at)
                                <div>
                                    <p class="text-brand-gray-medium text-xs tracking-wide mb-1">วันที่จัดส่ง</p>
                                    <p class="text-brand-black">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                            @if($order->completed_at)
                                <div>
                                    <p class="text-brand-gray-medium text-xs tracking-wide mb-1">วันที่สำเร็จ</p>
                                    <p class="text-brand-black">{{ $order->completed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Items --}}
                <div class="bg-white border border-brand-gray-border p-6">
                    <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-4">
                        รายการสินค้า
                    </h3>
                    <div class="divide-y divide-brand-gray-border">
                        @foreach($order->items as $item)
                            <div class="py-4 flex gap-4">
                                <div class="flex-shrink-0 w-16 h-20 bg-brand-gray overflow-hidden">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->primaryImage->image_path) }}"
                                             alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-brand-black">{{ $item->product_name }}</p>
                                    @if($item->variant_label)
                                        <p class="text-xs text-brand-gray-medium mt-0.5">{{ $item->variant_label }}</p>
                                    @endif
                                    <p class="text-xs text-brand-gray-medium mt-1">
                                        ฿{{ number_format($item->price, 0) }} x {{ $item->quantity }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-sm font-medium text-brand-black">
                                        ฿{{ number_format($item->price * $item->quantity, 0) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price Summary --}}
                    <div class="mt-4 pt-4 border-t border-brand-gray-border space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-brand-gray-medium">ยอดรวมสินค้า</span>
                            <span class="text-brand-black">฿{{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-brand-gray-medium">ค่าจัดส่ง</span>
                            <span class="text-brand-black">฿{{ number_format($order->shipping_fee, 0) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-brand-gray-medium">ส่วนลด</span>
                                <span class="text-green-600">-฿{{ number_format($order->discount, 0) }}</span>
                            </div>
                        @endif
                        @if($order->points_used > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-brand-gray-medium">แต้มที่ใช้</span>
                                <span class="text-green-600">-฿{{ number_format($order->points_used, 0) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-medium pt-2 border-t border-brand-gray-border">
                            <span class="text-brand-black">ยอดรวมทั้งหมด</span>
                            <span class="text-brand-black">฿{{ number_format($order->total, 0) }}</span>
                        </div>
                        @if($order->points_earned > 0)
                            <p class="text-xs text-brand-gray-medium text-right">
                                แต้มที่ได้รับ: +{{ number_format($order->points_earned) }} แต้ม
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white border border-brand-gray-border p-6">
                    <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-4">
                        ที่อยู่จัดส่ง
                    </h3>
                    <div class="text-sm text-brand-gray-dark space-y-1">
                        <p class="font-medium text-brand-black">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_district }} {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                    </div>
                </div>

                {{-- Payment Slip --}}
                @if($order->paymentSlip)
                    <div class="bg-white border border-brand-gray-border p-6">
                        <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-4">
                            สลิปการโอน
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <div class="flex-shrink-0">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($order->paymentSlip->image_path) }}"
                                     alt="สลิปการโอน"
                                     class="w-48 h-auto border border-brand-gray-border">
                            </div>
                            <div class="text-sm space-y-2">
                                <div>
                                    <p class="text-xs text-brand-gray-medium tracking-wide">อัปโหลดเมื่อ</p>
                                    <p class="text-brand-black">{{ $order->paymentSlip->uploaded_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                </div>
                                @if($order->paymentSlip->confirmed_at)
                                    <div>
                                        <p class="text-xs text-brand-gray-medium tracking-wide">อนุมัติเมื่อ</p>
                                        <p class="text-green-600">{{ $order->paymentSlip->confirmed_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endif
                                @if($order->paymentSlip->rejection_reason)
                                    <div>
                                        <p class="text-xs text-brand-gray-medium tracking-wide">เหตุผลการปฏิเสธ</p>
                                        <p class="text-red-500">{{ $order->paymentSlip->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Upload Slip --}}
                @if(in_array($order->status, ['pending', 'awaiting_payment']))
                    <div class="bg-white border border-brand-gray-border p-6">
                        <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-2">
                            {{ $order->paymentSlip ? 'อัปโหลดสลิปใหม่' : 'แนบสลิปการโอน' }}
                        </h3>
                        @if($order->status === 'awaiting_payment')
                            <p class="text-xs text-blue-600 mb-4">สลิปของคุณอยู่ระหว่างการตรวจสอบ หากต้องการส่งสลิปใหม่ให้อัปโหลดอีกครั้ง</p>
                        @else
                            <p class="text-xs text-brand-gray-medium mb-4">กรุณาโอนเงินและแนบสลิปเพื่อยืนยันการชำระเงิน</p>
                        @endif
                        <form method="POST" action="{{ route('orders.slip.store', $order) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col sm:flex-row gap-3 items-start">
                                <input type="file" name="slip" accept="image/*"
                                       class="block text-sm text-brand-gray-dark file:mr-4 file:py-2 file:px-4 file:border file:border-brand-gray-border file:text-xs file:font-medium file:tracking-wide file:bg-white file:text-brand-black hover:file:bg-brand-gray file:cursor-pointer file:transition-colors file:duration-150"
                                       required>
                                <button type="submit"
                                        class="flex-shrink-0 px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-brown transition-colors duration-300">
                                    อัปโหลดสลิป
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>
        </div>

    </div>

</x-layouts.shop>
