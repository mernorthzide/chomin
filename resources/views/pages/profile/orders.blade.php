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

                <div class="bg-white border border-brand-gray-border p-6 md:p-8">

                    <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-6">
                        ประวัติสั่งซื้อ
                    </h2>

                    @if($orders->isEmpty())
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-brand-gray-border mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-brand-gray-medium text-sm mb-4">ยังไม่มีประวัติการสั่งซื้อ</p>
                            <a href="{{ route('shop.index') }}"
                               class="inline-block px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-brown transition-colors duration-300">
                                เลือกซื้อสินค้า
                            </a>
                        </div>
                    @else
                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-brand-gray-border">
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">เลขออเดอร์</th>
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">วันที่</th>
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">สถานะ</th>
                                        <th class="text-right text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3">ยอดรวม</th>
                                        <th class="pb-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-gray-border">
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="py-4 pr-4 font-medium text-brand-black">
                                                {{ $order->order_number }}
                                            </td>
                                            <td class="py-4 pr-4 text-brand-gray-dark">
                                                {{ $order->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="py-4 pr-4">
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
                                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded {{ $statusColor }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td class="py-4 text-right font-medium text-brand-black">
                                                ฿{{ number_format($order->total, 0) }}
                                            </td>
                                            <td class="py-4 pl-4 text-right">
                                                <a href="{{ route('orders.show', $order) }}"
                                                   class="text-xs text-brand-gray-dark hover:text-brand-black underline tracking-wide transition-colors duration-150">
                                                    ดูรายละเอียด
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="md:hidden space-y-4">
                            @foreach($orders as $order)
                                <div class="border border-brand-gray-border p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-medium text-brand-black">{{ $order->order_number }}</p>
                                            <p class="text-xs text-brand-gray-medium mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
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
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded {{ $statusColor }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-brand-black">฿{{ number_format($order->total, 0) }}</p>
                                        <a href="{{ route('orders.show', $order) }}"
                                           class="text-xs text-brand-gray-dark hover:text-brand-black underline tracking-wide">
                                            ดูรายละเอียด
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($orders->hasPages())
                            <div class="mt-6">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    @endif

                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
