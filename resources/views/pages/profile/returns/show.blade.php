<x-layouts.shop :title="$return->rma_number.' | CHOMIN'" :noindex="true">
    @php $isEn = app()->getLocale() === 'en'; @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <a href="{{ route('returns.index', ['locale' => app()->getLocale()]) }}"
           class="text-xs uppercase tracking-[0.14em] text-brand-gray-medium hover:text-brand-black">
            ← {{ $isEn ? 'All returns' : 'รายการคืนทั้งหมด' }}
        </a>

        <div class="mt-3 flex items-start justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">{{ $return->rma_number }}</p>
                <h1 class="mt-1 font-serif text-3xl md:text-4xl uppercase">
                    {{ $return->type === 'exchange' ? ($isEn ? 'Exchange' : 'เปลี่ยนสินค้า') : ($isEn ? 'Return' : 'คืนสินค้า') }}
                </h1>
            </div>
            <span class="text-[10px] uppercase tracking-[0.14em] border border-brand-black px-2 py-1 whitespace-nowrap">
                {{ $return->status_label }}
            </span>
        </div>

        @if(session('flash'))
            <div class="mt-4 border border-brand-black px-4 py-3 text-sm">
                {{ session('flash')['message'] }}
            </div>
        @endif

        <dl class="mt-6 grid grid-cols-2 gap-y-3 border-y border-brand-gray-border py-5 text-sm">
            <dt class="text-brand-gray-medium">{{ $isEn ? 'Order' : 'คำสั่งซื้อ' }}</dt>
            <dd>
                <a href="{{ route('orders.show', ['locale' => app()->getLocale(), 'order' => $return->order_id]) }}"
                   class="underline">
                    {{ $return->order?->order_number }}
                </a>
            </dd>
            <dt class="text-brand-gray-medium">{{ $isEn ? 'Reason' : 'เหตุผล' }}</dt>
            <dd>{{ $return->reason_label }}</dd>
            @if($return->refund_amount > 0)
                <dt class="text-brand-gray-medium">{{ $isEn ? 'Refund amount' : 'จำนวนเงินคืน' }}</dt>
                <dd>฿{{ number_format($return->refund_amount, 0) }}</dd>
            @endif
            <dt class="text-brand-gray-medium">{{ $isEn ? 'Submitted' : 'ส่งคำขอ' }}</dt>
            <dd>{{ $return->created_at->isoFormat('LLL') }}</dd>
        </dl>

        @if($return->reason_detail)
            <div class="mt-5">
                <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Detail' : 'รายละเอียด' }}</p>
                <p class="mt-1 text-sm whitespace-pre-line">{{ $return->reason_detail }}</p>
            </div>
        @endif

        <div class="mt-6">
            <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-2">{{ $isEn ? 'Items' : 'รายการ' }}</p>
            <div class="space-y-2">
                @foreach($return->items as $item)
                    <div class="border border-brand-gray-border p-3 flex items-start justify-between gap-3 text-sm">
                        <div>
                            <p>{{ $item['name'] }}</p>
                            <p class="text-xs text-brand-gray-medium">{{ collect([$item['color'] ?? null, $item['size'] ?? null])->filter()->implode(' / ') }}</p>
                        </div>
                        <p class="whitespace-nowrap">x{{ $item['quantity'] }} · ฿{{ number_format($item['price'], 0) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if(!empty($return->photos))
            <div class="mt-6">
                <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-2">{{ $isEn ? 'Photos' : 'รูปภาพ' }}</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($return->photos as $photo)
                        <a href="{{ Storage::url($photo) }}" target="_blank" rel="noopener">
                            <img src="{{ Storage::url($photo) }}" alt="" class="w-full aspect-square object-cover" loading="lazy">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($return->admin_note)
            <div class="mt-6 border-l-2 border-brand-black bg-brand-gray/40 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'CHOMIN replied' : 'CHOMIN ตอบ' }}</p>
                <p class="text-sm whitespace-pre-line">{{ $return->admin_note }}</p>
            </div>
        @endif

        @if(in_array($return->status, ['requested', 'approved']))
            <form method="POST" action="{{ route('returns.cancel', ['locale' => app()->getLocale(), 'return' => $return->id]) }}" class="mt-8">
                @csrf
                <button type="submit"
                        onclick="return confirm('{{ $isEn ? 'Cancel this return request?' : 'ยกเลิกคำขอนี้?' }}')"
                        class="border border-brand-black px-4 py-2 text-xs uppercase tracking-[0.14em] hover:bg-brand-black hover:text-white">
                    {{ $isEn ? 'Cancel request' : 'ยกเลิกคำขอ' }}
                </button>
            </form>
        @endif
    </div>
</x-layouts.shop>
