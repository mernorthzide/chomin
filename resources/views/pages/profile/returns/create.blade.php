<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Request Return' : 'แจ้งคืนสินค้า').' | CHOMIN'" :noindex="true">
    @php $isEn = app()->getLocale() === 'en'; @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <a href="{{ route('orders.show', ['locale' => app()->getLocale(), 'order' => $order->id]) }}"
           class="text-xs uppercase tracking-[0.14em] text-brand-gray-medium hover:text-brand-black">
            ← {{ $isEn ? 'Back to order' : 'กลับไปคำสั่งซื้อ' }}
        </a>
        <h1 class="mt-3 font-serif text-3xl md:text-4xl uppercase">
            {{ $isEn ? 'Request return / exchange' : 'แจ้งคืน / เปลี่ยนสินค้า' }}
        </h1>
        <p class="mt-2 text-sm text-brand-gray-medium">
            {{ $isEn ? 'Order' : 'คำสั่งซื้อ' }} {{ $order->order_number }} ·
            {{ $isEn ? '30-day window from delivery.' : 'ภายใน 30 วันนับจากวันที่จัดส่ง' }}
        </p>

        @if($errors->any())
            <div class="mt-4 border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('returns.store', ['locale' => app()->getLocale(), 'order' => $order->id]) }}"
              enctype="multipart/form-data"
              class="mt-8 space-y-6">
            @csrf

            <fieldset>
                <legend class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-3">
                    {{ $isEn ? 'Type' : 'ประเภท' }}
                </legend>
                <div class="grid grid-cols-2 gap-2">
                    <label class="border border-brand-gray-border p-3 cursor-pointer flex items-center gap-2 has-[:checked]:border-brand-black has-[:checked]:bg-brand-black has-[:checked]:text-white">
                        <input type="radio" name="type" value="return" checked class="accent-brand-black">
                        <span class="text-sm">{{ $isEn ? 'Return (refund)' : 'คืนเงิน' }}</span>
                    </label>
                    <label class="border border-brand-gray-border p-3 cursor-pointer flex items-center gap-2 has-[:checked]:border-brand-black has-[:checked]:bg-brand-black has-[:checked]:text-white">
                        <input type="radio" name="type" value="exchange" class="accent-brand-black">
                        <span class="text-sm">{{ $isEn ? 'Exchange (different size/color)' : 'เปลี่ยนไซส์ / สี' }}</span>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-3">
                    {{ $isEn ? 'Items to return' : 'รายการที่ต้องการคืน/เปลี่ยน' }}
                </legend>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                        <label class="flex items-center gap-3 border border-brand-gray-border p-3 cursor-pointer has-[:checked]:border-brand-black">
                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="accent-brand-black">
                            <div class="w-16 h-20 bg-brand-gray overflow-hidden shrink-0">
                                @if($item->product?->primaryImage)
                                    <img src="{{ Storage::url($item->product->primaryImage->image_path) }}"
                                         alt="" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">{{ $item->product_name }}</p>
                                <p class="text-xs text-brand-gray-medium">
                                    {{ $item->variant_label }} · {{ $isEn ? 'Qty' : 'จำนวน' }} {{ $item->quantity }}
                                </p>
                                <p class="text-xs">฿{{ number_format($item->price, 0) }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-3">
                    {{ $isEn ? 'Reason' : 'เหตุผล' }}
                </legend>
                <select name="reason" required class="w-full border border-brand-gray-border px-3 py-2 text-sm bg-white">
                    <option value="size_too_small">{{ $isEn ? 'Size too small' : 'ไซส์เล็กไป' }}</option>
                    <option value="size_too_large">{{ $isEn ? 'Size too large' : 'ไซส์ใหญ่ไป' }}</option>
                    <option value="color_different">{{ $isEn ? 'Color looks different' : 'สีไม่ตรงตามภาพ' }}</option>
                    <option value="defective">{{ $isEn ? 'Defective / damaged' : 'สินค้าชำรุด' }}</option>
                    <option value="not_as_described">{{ $isEn ? 'Not as described' : 'ไม่ตรงกับรายละเอียด' }}</option>
                    <option value="changed_mind">{{ $isEn ? 'Changed mind' : 'เปลี่ยนใจ' }}</option>
                    <option value="other">{{ $isEn ? 'Other' : 'อื่น ๆ' }}</option>
                </select>
            </fieldset>

            <div>
                <label class="text-xs uppercase tracking-[0.14em] text-brand-gray-light">
                    {{ $isEn ? 'Detail (optional)' : 'รายละเอียดเพิ่มเติม (ถ้ามี)' }}
                </label>
                <textarea name="reason_detail" rows="3" maxlength="1000"
                          class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm"></textarea>
            </div>

            <div>
                <label class="text-xs uppercase tracking-[0.14em] text-brand-gray-light">
                    {{ $isEn ? 'Photos (optional, up to 6)' : 'รูปสินค้า (ถ้ามี ไม่เกิน 6 รูป)' }}
                </label>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                       class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-xs file:mr-3 file:border-0 file:bg-brand-black file:text-white file:px-3 file:py-1 file:text-[11px] file:uppercase">
                <p class="mt-1 text-[10px] text-brand-gray-light">
                    {{ $isEn ? 'Helpful for damaged or color-mismatch claims.' : 'แนะนำถ่ายให้เห็นรอยตำหนิ/สี เพื่อพิจารณาเร็วขึ้น' }}
                </p>
            </div>

            <div class="border border-brand-gray-border bg-brand-gray/40 p-4 text-xs text-brand-gray-dark space-y-2">
                <p class="font-medium uppercase tracking-[0.14em]">{{ $isEn ? 'How returns work' : 'ขั้นตอนการคืน/เปลี่ยน' }}</p>
                <ol class="list-decimal pl-5 space-y-1">
                    <li>{{ $isEn ? 'Submit this form. We review within 1–2 business days.' : 'ส่งคำขอนี้ ทีมงานตรวจสอบภายใน 1–2 วันทำการ' }}</li>
                    <li>{{ $isEn ? 'Pack the items unused with original tags.' : 'แพ็คสินค้าให้อยู่ในสภาพเดิม พร้อมป้ายตามที่ได้รับ' }}</li>
                    <li>{{ $isEn ? 'We send pickup or drop-off instructions via LINE.' : 'ทีมงานแจ้งวิธีจัดส่ง/รับสินค้าทาง LINE' }}</li>
                    <li>{{ $isEn ? 'Refund processed within 7 days after we receive the items.' : 'คืนเงินภายใน 7 วันหลังได้รับสินค้า' }}</li>
                </ol>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-brand-black px-5 py-3 text-xs uppercase tracking-[0.16em] text-white">
                    {{ $isEn ? 'Submit request' : 'ส่งคำขอ' }}
                </button>
                <a href="{{ route('orders.show', ['locale' => app()->getLocale(), 'order' => $order->id]) }}"
                   class="border border-brand-black px-5 py-3 text-xs uppercase tracking-[0.16em]">
                    {{ $isEn ? 'Cancel' : 'ยกเลิก' }}
                </a>
            </div>
        </form>
    </div>
</x-layouts.shop>
