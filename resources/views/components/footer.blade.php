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
                <h4 class="text-xs font-bold uppercase tracking-widest">ช่วยเหลือและข้อมูล</h4>
                <ul class="text-xs tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <span>จัดส่งฟรีทั่วประเทศ</span>
                    </li>
                    <li>
                        <span>เปลี่ยนคืนภายใน 30 วัน</span>
                    </li>
                    @if(\App\Models\SiteSetting::get('contact_phone'))
                    <li>
                        <a href="tel:{{ \App\Models\SiteSetting::get('contact_phone') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            {{ \App\Models\SiteSetting::get('contact_phone') }}
                        </a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('contact_email'))
                    <li>
                        <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            {{ \App\Models\SiteSetting::get('contact_email') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Column 2: About -->
            <div class="space-y-6">
                <h4 class="text-xs font-bold uppercase tracking-widest">เกี่ยวกับเรา</h4>
                <ul class="text-xs tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">เรื่องราวของเรา</a>
                    </li>
                    <li>
                        <a href="{{ route('collections.index') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">คอลเล็คชัน</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">ร้านค้า</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Social Media -->
            <div class="space-y-6">
                <h4 class="text-xs font-bold uppercase tracking-widest">โซเชียลมีเดีย</h4>
                <ul class="text-xs tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <a href="https://www.facebook.com/Chominstyle" target="_blank" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Facebook</a>
                    </li>
                    <li>
                        <a href="https://line.me/R/ti/p/@chomin.th" target="_blank" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">LINE: @chomin.th</a>
                    </li>
                    <li>
                        <a href="mailto:chomin.ecommer@gmail.com" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">chomin.ecommer@gmail.com</a>
                    </li>
                    @if(\App\Models\SiteSetting::get('social_instagram'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('social_instagram') }}" target="_blank" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Instagram</a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Column 4: Size Guide & Contact -->
            <div class="space-y-6">
                <h4 class="text-xs font-bold uppercase tracking-widest">ไซส์และการสั่งซื้อ</h4>
                <ul class="text-xs tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <span>ไซส์ XS &ndash; 6XL</span>
                    </li>
                    <li>
                        <span>50+ สี ให้เลือก</span>
                    </li>
                    <li>
                        <a href="https://lin.ee/chomin" target="_blank" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            สั่งซื้อผ่าน LINE: @chomin.th
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center pt-10 border-t border-brand-gray-border/50 gap-6">
            <div class="flex gap-8 text-xs font-semibold tracking-[0.2em] uppercase">
                <a href="#" class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:underline">นโยบายความเป็นส่วนตัว</a>
                <a href="#" class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:underline">เงื่อนไขการใช้งาน</a>
            </div>
            <span class="text-xs tracking-[0.2em] text-brand-gray-light uppercase">
                &copy; {{ date('Y') }} CHOMIN. สงวนลิขสิทธิ์ทุกประการ.
            </span>
        </div>
    </div>
</footer>
