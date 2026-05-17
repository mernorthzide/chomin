<x-layouts.shop :title="app()->getLocale() === 'en' ? 'Returns & Exchanges' : 'คืน / เปลี่ยนสินค้า'" :noindex="true">
    @php $isEn = app()->getLocale() === 'en'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            {{ $isEn ? 'My Account' : 'บัญชีของฉัน' }}
        </h1>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            <div class="lg:col-span-3">
                <div class="bg-white border border-brand-gray-border p-6 md:p-8">
                    <div class="flex items-end justify-between mb-6">
                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black">
                            {{ $isEn ? 'Returns & Exchanges' : 'คืน / เปลี่ยนสินค้า' }}
                        </h2>
                        <a href="{{ route('pages.returns') }}" class="text-xs underline text-brand-gray-medium">
                            {{ $isEn ? 'Return policy' : 'นโยบายการคืน' }}
                        </a>
                    </div>

                    @if(session('flash'))
                        <div class="mb-6 border border-brand-black px-4 py-3 text-sm">
                            {{ session('flash')['message'] }}
                        </div>
                    @endif

                    @if($returns->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-brand-gray-medium text-sm mb-4">
                                {{ $isEn ? 'No return requests yet.' : 'ยังไม่มีคำขอคืน/เปลี่ยนสินค้า' }}
                            </p>
                            <a href="{{ route('orders.index') }}"
                               class="inline-block px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase">
                                {{ $isEn ? 'View orders' : 'ดูคำสั่งซื้อ' }}
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($returns as $ret)
                                <a href="{{ route('returns.show', ['locale' => app()->getLocale(), 'return' => $ret->id]) }}"
                                   class="block border border-brand-gray-border p-4 hover:border-brand-black">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-light">{{ $ret->rma_number }}</p>
                                            <p class="mt-1 text-sm">
                                                {{ $ret->type === 'exchange' ? ($isEn ? 'Exchange' : 'เปลี่ยนสินค้า') : ($isEn ? 'Return' : 'คืนสินค้า') }}
                                                · {{ $ret->reason_label }}
                                            </p>
                                            <p class="mt-1 text-xs text-brand-gray-medium">
                                                {{ $isEn ? 'Order' : 'คำสั่งซื้อ' }} {{ $ret->order?->order_number }}
                                                · {{ $ret->created_at->isoFormat('LL') }}
                                            </p>
                                        </div>
                                        <span class="text-[10px] uppercase tracking-[0.14em] border border-brand-black px-2 py-1 whitespace-nowrap">
                                            {{ $ret->status_label }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if($returns->hasPages())
                            <div class="mt-6">{{ $returns->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
