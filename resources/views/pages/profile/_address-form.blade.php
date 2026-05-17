<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    <div>
        <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
            ชื่อผู้รับ <span class="text-red-400">*</span>
        </label>
        <input type="text" name="name"
               value="{{ old('name', $address->name ?? '') }}"
               class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white"
               required>
    </div>

    <div>
        <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
            เบอร์โทร <span class="text-red-400">*</span>
        </label>
        <input type="text" name="phone"
               value="{{ old('phone', $address->phone ?? '') }}"
               class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white"
               required>
    </div>

    <div class="sm:col-span-2">
        <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
            ที่อยู่ <span class="text-red-400">*</span>
        </label>
        <textarea name="address" rows="2"
                  class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white resize-none"
                  required>{{ old('address', $address->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
            อำเภอ/เขต <span class="text-red-400">*</span>
        </label>
        <input type="text" name="district"
               value="{{ old('district', $address->district ?? '') }}"
               class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white"
               required>
    </div>

    <div x-data="thaiAddressLookup({
            initialPostal: '{{ old('postal_code', $address->postal_code ?? '') }}',
            initialProvince: '{{ old('province', $address->province ?? '') }}',
         })" class="contents">
        <div>
            <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
                จังหวัด <span class="text-red-400">*</span>
            </label>
            <select name="province" x-model="province"
                    class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white"
                    required>
                <option value="">-- เลือกจังหวัด --</option>
                @foreach(config('thai-locations.provinces') as $province)
                    <option value="{{ $province }}">{{ $province }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-1">
                รหัสไปรษณีย์ <span class="text-red-400">*</span>
            </label>
            <input type="text" inputmode="numeric" maxlength="5" pattern="\d{5}" name="postal_code"
                   x-model="postalCode" @input="autofill()"
                   class="w-full border border-brand-gray-border px-3 py-2 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white"
                   required>
            <p class="mt-1 text-[11px] text-brand-gray-light">กรอกรหัสไปรษณีย์แล้วระบบจะกรอกจังหวัดให้อัตโนมัติ</p>
        </div>
    </div>

    <script>
    function thaiAddressLookup(config) {
        return {
            postalCode: config.initialPostal,
            province: config.initialProvince,
            timer: null,
            autofill() {
                clearTimeout(this.timer);
                if (!/^\d{5}$/.test(this.postalCode)) return;
                this.timer = setTimeout(async () => {
                    try {
                        const locale = document.documentElement.lang || 'th';
                        const url = `/${locale}/shipping/lookup?postal_code=${this.postalCode}`;
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        if (data.ok && data.province && !this.province) {
                            this.province = data.province;
                        } else if (data.ok && data.province) {
                            this.province = data.province;
                        }
                    } catch (e) {}
                }, 200);
            }
        };
    }
    </script>

    <div class="flex items-center gap-3 sm:col-span-2">
        <input type="checkbox" id="is_default_{{ $address->id ?? 'new' }}" name="is_default" value="1"
               {{ old('is_default', ($address->is_default ?? false) ? 1 : 0) ? 'checked' : '' }}
               class="w-4 h-4 border-brand-gray-border text-brand-black focus:ring-0">
        <label for="is_default_{{ $address->id ?? 'new' }}" class="text-sm text-brand-gray-dark cursor-pointer">
            ตั้งเป็นที่อยู่เริ่มต้น
        </label>
    </div>

</div>
