<x-layouts.shop :title="app()->getLocale() === 'en' ? 'Checkout' : 'ชำระเงิน'" :noindex="true">

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

                        {{-- Trust signals --}}
                        <div class="mt-5 space-y-3 border border-brand-gray-border p-4">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 flex-shrink-0 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.12em]">การชำระเงินปลอดภัย</p>
                                    <p class="text-[11px] text-brand-gray-medium">เข้ารหัส SSL · ตรวจสอบสลิปทุกใบ</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 flex-shrink-0 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                </svg>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.12em]">รับประกันคุณภาพ</p>
                                    <p class="text-[11px] text-brand-gray-medium">QC ทุกตัว · เปลี่ยน-คืนภายใน 30 วัน</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 flex-shrink-0 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                </svg>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.12em]">ติดต่อเราได้เสมอ</p>
                                    <a href="https://line.me/R/ti/p/@chomin.th" target="_blank" rel="noopener" class="text-[11px] text-brand-black underline-offset-2 underline">LINE @chomin.th</a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-3 border border-brand-gray-border text-center">
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
