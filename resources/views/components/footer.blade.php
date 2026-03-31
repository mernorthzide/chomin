<footer class="bg-white border-t border-brand-gray-border">
    <div class="px-6 md:px-12 py-16">

        <!-- Logo -->
        <div class="mb-16">
            <x-brand-logo variant="dark" class="h-8 md:h-10" />
        </div>

        <!-- Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">

            <!-- Column 1: Help & Info -->
            <div class="space-y-6">
                <h4 class="text-[11px] font-bold uppercase tracking-widest">ช่วยเหลือและข้อมูล</h4>
                <ul class="text-[11px] tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <a href="#" class="hover:text-brand-black transition-colors duration-200">การจัดส่ง</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-brand-black transition-colors duration-200">การเปลี่ยนคืนสินค้า</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-brand-black transition-colors duration-200">คำถามที่พบบ่อย</a>
                    </li>
                    @if(\App\Models\SiteSetting::get('contact_phone'))
                    <li>
                        <a href="tel:{{ \App\Models\SiteSetting::get('contact_phone') }}" class="hover:text-brand-black transition-colors duration-200">
                            {{ \App\Models\SiteSetting::get('contact_phone') }}
                        </a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('contact_email'))
                    <li>
                        <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email') }}" class="hover:text-brand-black transition-colors duration-200">
                            {{ \App\Models\SiteSetting::get('contact_email') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Column 2: About -->
            <div class="space-y-6">
                <h4 class="text-[11px] font-bold uppercase tracking-widest">เกี่ยวกับเรา</h4>
                <ul class="text-[11px] tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-brand-black transition-colors duration-200">เรื่องราวของเรา</a>
                    </li>
                    <li>
                        <a href="{{ route('collections.index') }}" class="hover:text-brand-black transition-colors duration-200">คอลเล็คชัน</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}" class="hover:text-brand-black transition-colors duration-200">ร้านค้า</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Social Media -->
            <div class="space-y-6">
                <h4 class="text-[11px] font-bold uppercase tracking-widest">โซเชียลมีเดีย</h4>
                <ul class="text-[11px] tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    @if(\App\Models\SiteSetting::get('social_instagram'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('social_instagram') }}" target="_blank" class="hover:text-brand-black transition-colors duration-200">Instagram</a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('social_facebook'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('social_facebook') }}" target="_blank" class="hover:text-brand-black transition-colors duration-200">Facebook</a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('social_line'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('social_line') }}" target="_blank" class="hover:text-brand-black transition-colors duration-200">LINE</a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Column 4: Newsletter -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-widest mb-6">จดหมายข่าว</h4>
                <p class="text-[11px] tracking-widest text-brand-gray-medium mb-6 uppercase leading-relaxed">
                    รับข่าวสารคอลเลกชันใหม่และสิทธิพิเศษสำหรับคุณ
                </p>
                <form class="flex border-b border-brand-black pb-2" onsubmit="return false;">
                    <input
                        type="email"
                        placeholder="ที่อยู่อีเมล"
                        class="bg-transparent border-none text-[10px] w-full focus:ring-0 px-0 placeholder:text-brand-gray-light uppercase tracking-widest">
                    <button type="submit" class="text-[10px] font-bold uppercase tracking-widest flex-shrink-0 hover:opacity-60 transition-opacity">
                        สมัครสมาชิก
                    </button>
                </form>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center pt-10 border-t border-brand-gray-border/50 gap-6">
            <div class="flex gap-8 text-[10px] font-semibold tracking-[0.2em] uppercase">
                <a href="#" class="hover:opacity-60 transition-opacity duration-200">นโยบายความเป็นส่วนตัว</a>
                <a href="#" class="hover:opacity-60 transition-opacity duration-200">เงื่อนไขการใช้งาน</a>
            </div>
            <span class="text-[10px] tracking-[0.2em] text-brand-gray-light uppercase">
                &copy; {{ date('Y') }} CHOMIN. สงวนลิขสิทธิ์ทุกประการ.
            </span>
        </div>
    </div>
</footer>
