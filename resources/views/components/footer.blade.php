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
                        <a href="{{ route('pages.shipping') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">จัดส่งฟรีทั่วประเทศ</a>
                    </li>
                    <li>
                        <a href="{{ route('pages.returns') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">เปลี่ยนคืนภายใน 30 วัน</a>
                    </li>
                    <li><a href="{{ route('faq') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">คำถามที่พบบ่อย</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">ติดต่อเรา</a></li>
                    @if(\App\Models\SiteSetting::get('site_phone'))
                    <li>
                        <a href="tel:{{ \App\Models\SiteSetting::get('site_phone') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            {{ \App\Models\SiteSetting::get('site_phone') }}
                        </a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('site_email'))
                    <li>
                        <a href="mailto:{{ \App\Models\SiteSetting::get('site_email') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            {{ \App\Models\SiteSetting::get('site_email') }}
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
                    <li>
                        <a href="{{ route('stories.index') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">บทความ</a>
                    </li>
                    <li>
                        <a href="{{ route('stores') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">{{ app()->getLocale() === 'en' ? 'Store Locator' : 'หาร้านค้า' }}</a>
                    </li>
                    <li>
                        <a href="{{ route('pages.member') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Member Program</a>
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
                    @if(\App\Models\SiteSetting::get('line_chat_url'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('line_chat_url') }}" target="_blank" rel="noopener" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">LINE</a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('site_email'))
                    <li>
                        <a href="mailto:{{ \App\Models\SiteSetting::get('site_email') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">{{ \App\Models\SiteSetting::get('site_email') }}</a>
                    </li>
                    @endif
                    @if(\App\Models\SiteSetting::get('instagram_url'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('instagram_url') }}" target="_blank" rel="noopener" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Instagram</a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Column 4: Size Guide & Contact -->
            <div class="space-y-6">
                <h4 class="text-xs font-bold uppercase tracking-widest">ไซส์และการสั่งซื้อ</h4>
                <ul class="text-xs tracking-widest text-brand-gray-medium space-y-3 uppercase">
                    <li>
                        <a href="{{ route('pages.size-guide') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">ไซส์ XS &ndash; 6XL</a>
                    </li>
                    <li>
                        <a href="{{ route('color-library') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">50+ สี ให้เลือก</a>
                    </li>
                    @if(\App\Models\SiteSetting::get('line_chat_url'))
                    <li>
                        <a href="{{ \App\Models\SiteSetting::get('line_chat_url') }}" target="_blank" rel="noopener" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">
                            สั่งซื้อผ่าน LINE
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('pages.careers') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Careers</a>
                    </li>
                    <li>
                        <a href="{{ route('pages.partnerships') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Partnerships</a>
                    </li>
                    <li>
                        <a href="{{ route('pages.wholesale') }}" class="hover:text-brand-black transition-colors duration-200 focus:outline-none focus:underline">Wholesale</a>
                    </li>
                </ul>
            </div>
        </div>

        @php
            $embedsEnabled = \App\Models\SiteSetting::get('external_embeds_enabled', '0') === '1';
            $instagramEmbed = \App\Models\SiteSetting::get('instagram_embed_html');
            $instagramUrl = \App\Models\SiteSetting::get('instagram_url');
        @endphp
        @if($embedsEnabled && ($instagramEmbed || $instagramUrl))
            <div class="mb-14 border-t border-brand-gray-border/50 pt-10"
                 x-data="{ allowEmbeds: false }"
                 x-init="try { allowEmbeds = !!JSON.parse(localStorage.getItem('chomin_cookie_consent') || '{}').embeds } catch (e) { allowEmbeds = false }">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-widest">Instagram</h4>
                    @if($instagramUrl)
                        <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="text-xs uppercase tracking-[0.15em] text-brand-gray-medium hover:text-brand-black">Open Instagram</a>
                    @endif
                </div>
                @if($instagramEmbed)
                    <template x-if="allowEmbeds">
                        <div class="max-w-xl">
                            {!! $instagramEmbed !!}
                        </div>
                    </template>
                    <p x-show="!allowEmbeds" class="text-xs text-brand-gray-medium">
                        Instagram embed จะแสดงหลังยอมรับคุกกี้หมวด embeds
                    </p>
                @endif
            </div>
        @endif

        <div class="mb-14 max-w-md">
            <h4 class="text-xs font-bold uppercase tracking-widest mb-4">รับข่าวสารจาก CHOMIN</h4>
            <form method="POST" action="{{ route('newsletter.store') }}" class="flex gap-2">
                @csrf
                <input type="email" name="email" required placeholder="{{ app()->getLocale() === 'en' ? 'Email address' : 'อีเมลของคุณ' }}" class="min-h-[44px] min-w-0 flex-1 border-brand-gray-border text-sm focus:border-brand-black focus:ring-brand-black">
                <button type="submit" class="min-h-[44px] bg-brand-black px-5 text-xs uppercase tracking-[0.15em] text-white transition-colors hover:bg-brand-gray-dark">Join</button>
            </form>
        </div>

        <!-- Copyright Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center pt-10 border-t border-brand-gray-border/50 gap-6">
            <div class="flex flex-wrap justify-center gap-6 text-xs font-semibold tracking-[0.15em] uppercase">
                <a href="{{ route('pages.privacy') }}" class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:underline">Privacy</a>
                <a href="{{ route('pages.terms') }}" class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:underline">Terms</a>
                <a href="{{ route('pages.gift-cards') }}" class="hover:opacity-60 transition-opacity duration-200 focus:outline-none focus:underline">Gift Cards</a>
            </div>
            <span class="text-xs tracking-[0.2em] text-brand-gray-light uppercase">
                &copy; {{ date('Y') }} CHOMIN &mdash; สงวนลิขสิทธิ์ทุกประการ
            </span>
        </div>
    </div>
</footer>
