<x-layouts.shop>

    <div class="px-6 md:px-12 py-10 md:py-14">

        {{-- Page Title --}}
        <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light mb-4">Checkout</p>
        <h1 class="font-serif text-5xl md:text-7xl uppercase leading-none text-brand-black mb-8">
            ชำระเงิน
        </h1>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
            $customOptionGroups = config('chomin.custom_options');
            $customOptionLabel = function (?array $options) use ($customOptionGroups): array {
                if (!$options) {
                    return [];
                }

                return collect($customOptionGroups)
                    ->map(function ($group, $key) use ($options) {
                        $value = $options[$key] ?? null;

                        if (!$value || !isset($group['options'][$value])) {
                            return null;
                        }

                        return $group['label'].': '.$group['options'][$value];
                    })
                    ->filter()
                    ->values()
                    ->all();
            };
        @endphp

        <form action="{{ route('checkout.store') }}" method="POST"
              x-init="init()"
              x-data="{
                  useNewAddress: {{ $addresses->isEmpty() ? 'true' : 'false' }},
                  selectedAddress: {{ $defaultAddress?->id ?? 'null' }},
                  addresses: {{ $addresses->toJson() }},
                  couponCode: '{{ request('coupon_code', '') }}',
                  pointsUsed: {{ request('points_used', 0) }},
                  maxPoints: {{ (int) (auth()->user()?->points ?? 0) }},
                  giftCardCodes: [''],

                  init() {
                      if (!this.useNewAddress && this.selectedAddress) {
                          const address = this.addresses.find((item) => item.id === this.selectedAddress);

                          if (address) {
                              this.fillFromAddress(address);
                          }
                      }
                  },

                  fillFromAddress(addr) {
                      this.$refs.shippingName.value = addr.name;
                      this.$refs.shippingPhone.value = addr.phone;
                      this.$refs.shippingAddress.value = addr.address;
                      this.$refs.shippingDistrict.value = addr.district;
                      this.$refs.shippingProvince.value = addr.province;
                      this.$refs.shippingPostalCode.value = addr.postal_code;
                  }
              }">
            @csrf

            {{-- Hidden fields for coupon and points --}}
            <input type="hidden" name="coupon_code" :value="couponCode">
            <input type="hidden" name="points_used" :value="pointsUsed">

            <div class="lg:grid lg:grid-cols-3 lg:gap-12">

                {{-- Left: Shipping Address --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Saved Addresses --}}
                    @if($addresses->isNotEmpty())
                        <div>
                            <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-4">
                                ที่อยู่จัดส่ง
                            </h2>

                            {{-- Address Cards --}}
                            <div class="space-y-3 mb-4">
                                @foreach($addresses as $address)
                                    <label class="flex items-start gap-3 p-4 border cursor-pointer transition-colors duration-200"
                                           :class="!useNewAddress && selectedAddress === {{ $address->id }} ? 'border-brand-black bg-white' : 'border-brand-gray-border hover:border-brand-gray-dark'">
                                        <input type="radio"
                                               name="_address_id"
                                               value="{{ $address->id }}"
                                               @change="useNewAddress = false; selectedAddress = {{ $address->id }}; fillFromAddress({{ json_encode($address) }})"
                                               {{ $address->is_default && $loop->first ? 'checked' : '' }}
                                               class="mt-0.5 text-brand-black focus:ring-brand-black">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-brand-black">
                                                {{ $address->name }}
                                                @if($address->is_default)
                                                    <span class="ml-2 text-xs text-brand-gray-medium">(ค่าเริ่มต้น)</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-brand-gray-medium mt-1">{{ $address->phone }}</p>
                                            <p class="text-xs text-brand-gray-medium mt-0.5">{{ $address->full_address }}</p>
                                        </div>
                                    </label>
                                @endforeach

                                {{-- New Address Option --}}
                                <label class="flex items-center gap-3 p-4 border cursor-pointer transition-colors duration-200"
                                       :class="useNewAddress ? 'border-brand-black bg-white' : 'border-brand-gray-border hover:border-brand-gray-dark'">
                                    <input type="radio"
                                           name="_address_id"
                                           value="new"
                                           @change="useNewAddress = true; selectedAddress = null"
                                           class="text-brand-black focus:ring-brand-black">
                                    <span class="text-sm text-brand-black">+ ใช้ที่อยู่ใหม่</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- Address Form --}}
                    <div x-show="useNewAddress" x-transition>
                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-4">
                            {{ $addresses->isEmpty() ? 'ที่อยู่จัดส่ง' : 'ที่อยู่ใหม่' }}
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    ชื่อผู้รับ <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="shipping_name" x-ref="shippingName"
                                       value="{{ old('shipping_name', auth()->user()->name) }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_name') border-red-400 @enderror">
                                @error('shipping_name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    เบอร์โทรศัพท์ <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="shipping_phone" x-ref="shippingPhone"
                                       value="{{ old('shipping_phone') }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_phone') border-red-400 @enderror">
                                @error('shipping_phone')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    ที่อยู่ <span class="text-red-500">*</span>
                                </label>
                                <textarea name="shipping_address" x-ref="shippingAddress"
                                          rows="2"
                                          class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_address') border-red-400 @enderror resize-none">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    อำเภอ/เขต <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="shipping_district" x-ref="shippingDistrict"
                                       value="{{ old('shipping_district') }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_district') border-red-400 @enderror">
                                @error('shipping_district')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    จังหวัด <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="shipping_province" x-ref="shippingProvince"
                                       value="{{ old('shipping_province') }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_province') border-red-400 @enderror">
                                @error('shipping_province')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    รหัสไปรษณีย์ <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="shipping_postal_code" x-ref="shippingPostalCode"
                                       value="{{ old('shipping_postal_code') }}"
                                       maxlength="10"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black @error('shipping_postal_code') border-red-400 @enderror">
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium tracking-wide uppercase text-brand-gray-dark mb-1">
                                    หมายเหตุ
                                </label>
                                <textarea name="note"
                                          rows="2"
                                          placeholder="หมายเหตุถึงผู้ขาย (ไม่บังคับ)"
                                          class="w-full border border-brand-gray-border px-4 py-3 text-sm focus:outline-none focus:border-brand-black resize-none">{{ old('note') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Coupon & Points Summary (mobile) --}}
                    <div class="lg:hidden border border-brand-gray-border bg-white p-4 space-y-3">
                        <div>
                            <p class="text-xs text-brand-gray-medium">รหัสคูปอง: <span class="text-brand-black" x-text="couponCode || 'ไม่มี'"></span></p>
                            <p class="text-xs text-brand-gray-medium mt-1">แต้มที่ใช้: <span class="text-brand-black" x-text="pointsUsed"></span></p>
                        </div>
                    </div>

                </div>

                {{-- Right: Order Summary --}}
                <div class="mt-10 lg:mt-0">
                    <div class="border border-brand-gray-border bg-white p-6 sticky top-28">
                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-6">
                            รายการสั่งซื้อ
                        </h2>

                        {{-- Items --}}
                        <div class="space-y-4 mb-6">
                            @foreach($cart->items as $item)
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-14 h-18 overflow-hidden bg-white">
                                        @if($item->product->primaryImage ?? false)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->primaryImage->image_path) }}"
                                                 alt="{{ $item->product->localized_name }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-brand-black truncate">{{ $item->product->localized_name }}</p>
                                        <p class="text-xs text-brand-gray-medium mt-0.5">
                                            @if($item->variant->color){{ $item->variant->color->localized_name }} / @endif{{ $item->variant->size }}
                                        </p>
                                        @if($optionSummary = $customOptionLabel($item->custom_options))
                                            <ul class="mt-1 space-y-0.5 text-[10px] uppercase tracking-[0.08em] text-brand-gray-medium">
                                                @foreach($optionSummary as $optionLine)
                                                    <li>{{ $optionLine }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <div class="flex justify-between mt-1">
                                            <span class="text-xs text-brand-gray-medium">x{{ $item->quantity }}</span>
                                            <span class="text-xs font-medium text-brand-black">฿{{ number_format($item->line_total, 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-brand-gray-border pt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-brand-gray-medium">ยอดรวมสินค้า</span>
                                <span>฿{{ number_format($cart->subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-brand-gray-medium">ค่าจัดส่ง</span>
                                <span class="text-xs italic text-brand-gray-medium">(คำนวณอัตโนมัติ)</span>
                            </div>
                            <template x-if="couponCode">
                                <div class="flex justify-between text-green-600">
                                    <span>คูปอง: <span x-text="couponCode"></span></span>
                                    <span>-</span>
                                </div>
                            </template>
                            <template x-if="pointsUsed > 0">
                                <div class="flex justify-between text-green-600">
                                    <span>แต้มสะสม (<span x-text="pointsUsed"></span> แต้ม)</span>
                                    <span>-</span>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-brand-gray-border mt-4 pt-4">
                            <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-3">
                                Gift Card
                            </label>
                            <template x-for="(code, index) in giftCardCodes" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <input type="text"
                                           :name="`gift_card_codes[${index}]`"
                                           x-model="giftCardCodes[index]"
                                           placeholder="CHOMIN-XXXX-XXXX"
                                           class="min-w-0 flex-1 border border-brand-gray-border px-3 py-2 text-xs focus:outline-none focus:border-brand-black bg-white">
                                    <button type="button"
                                            x-show="giftCardCodes.length > 1"
                                            @click="giftCardCodes.splice(index, 1)"
                                            class="px-3 text-xs border border-brand-gray-border">
                                        ลบ
                                    </button>
                                </div>
                            </template>
                            <button type="button"
                                    @click="giftCardCodes.push('')"
                                    class="text-xs underline text-brand-gray-medium hover:text-brand-black">
                                + เพิ่มบัตรอีกใบ
                            </button>
                        </div>

                        <div class="border-t border-brand-gray-border mt-4 pt-4">
                            <div class="flex justify-between font-medium">
                                <span class="text-sm">ยอดรวม</span>
                                <span class="text-lg">฿{{ number_format($cart->subtotal, 0) }}</span>
                            </div>
                        </div>

                        <button type="submit"
                                class="mt-6 w-full py-4 text-sm font-medium tracking-[0.15em] uppercase bg-brand-black text-white hover:bg-brand-gray-dark transition-colors duration-300">
                            ยืนยันสั่งซื้อ
                        </button>

                        <p class="mt-3 text-center text-xs text-brand-gray-medium">
                            ชำระเงินผ่าน PromptPay หลังยืนยันออเดอร์
                        </p>

                        <div class="mt-5 grid grid-cols-3 border border-brand-gray-border text-center">
                            <a href="{{ route('pages.shipping') }}" class="p-3 border-r border-brand-gray-border">
                                <span class="block text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">Ship</span>
                                <span class="block text-xs mt-1">Free</span>
                            </a>
                            <a href="{{ route('pages.returns') }}" class="p-3 border-r border-brand-gray-border">
                                <span class="block text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">Return</span>
                                <span class="block text-xs mt-1">30D</span>
                            </a>
                            <a href="{{ route('pages.member') }}" class="p-3">
                                <span class="block text-[10px] uppercase tracking-[0.12em] text-brand-gray-light">Point</span>
                                <span class="block text-xs mt-1">Earn</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

</x-layouts.shop>
