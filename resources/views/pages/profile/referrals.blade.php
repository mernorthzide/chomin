<x-layouts.shop :title="app()->getLocale() === 'en' ? 'Refer a Friend' : 'แนะนำเพื่อน'" :noindex="true">
    @php
        $isEn = app()->getLocale() === 'en';
        $user = auth()->user();
        $code = $user->referral_code;
        $shareUrl = url(app()->getLocale().'/r/'.$code);
        $tier = $user->tier;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            {{ $isEn ? 'My Account' : 'บัญชีของฉัน' }}
        </h1>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            <div class="lg:col-span-3 space-y-6">
                {{-- Tier panel --}}
                <div class="bg-white border border-brand-gray-border p-6 md:p-8">
                    <div class="flex items-baseline justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Your tier' : 'ระดับสมาชิก' }}</p>
                            <p class="mt-1 font-serif text-3xl md:text-4xl uppercase">{{ $tier['name'] }}</p>
                        </div>
                        <p class="text-xs uppercase tracking-[0.14em] text-brand-gray-medium text-right">
                            {{ $isEn ? 'Lifetime spend' : 'ยอดสะสม' }}<br>
                            <span class="text-base text-brand-black">฿{{ number_format($tier['lifetime_spend'], 0) }}</span>
                        </p>
                    </div>

                    @if($tier['next'])
                        <div class="mt-5">
                            <div class="h-1.5 w-full bg-brand-gray-border">
                                <div class="h-full bg-brand-black" style="width: {{ $tier['progress'] }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-brand-gray-medium">
                                {{ $isEn ? 'Spend' : 'อีก' }} <span class="text-brand-black">฿{{ number_format($tier['next']['to_next'], 0) }}</span>
                                {{ $isEn ? 'more to reach' : 'ก็ถึงระดับ' }} <span class="text-brand-black">{{ $tier['next']['name'] }}</span>
                            </p>
                        </div>
                    @else
                        <p class="mt-3 text-xs text-brand-gray-medium">{{ $isEn ? 'You are at our top tier — thank you.' : 'คุณอยู่ในระดับสูงสุดแล้ว ขอบคุณมาก' }}</p>
                    @endif

                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div class="border border-brand-gray-border p-3">
                            <p class="text-brand-gray-light uppercase tracking-[0.12em]">{{ $isEn ? 'Points' : 'แต้ม' }}</p>
                            <p class="mt-1 text-brand-black">×{{ $tier['multiplier'] }}</p>
                        </div>
                        <div class="border border-brand-gray-border p-3">
                            <p class="text-brand-gray-light uppercase tracking-[0.12em]">{{ $isEn ? 'Early access' : 'เข้าก่อนใคร' }}</p>
                            <p class="mt-1 text-brand-black">{{ $tier['early_access_days'] }} {{ $isEn ? 'days' : 'วัน' }}</p>
                        </div>
                        <div class="border border-brand-gray-border p-3">
                            <p class="text-brand-gray-light uppercase tracking-[0.12em]">{{ $isEn ? 'Birthday' : 'วันเกิด' }}</p>
                            <p class="mt-1 text-brand-black">+{{ $tier['birthday_bonus'] }} pts</p>
                        </div>
                        <div class="border border-brand-gray-border p-3">
                            <p class="text-brand-gray-light uppercase tracking-[0.12em]">{{ $isEn ? 'Shipping' : 'จัดส่ง' }}</p>
                            <p class="mt-1 text-brand-black">{{ $tier['shipping_perk'] ? ($isEn ? ucfirst($tier['shipping_perk']) : ($tier['shipping_perk'] === 'priority' ? 'ส่งก่อน' : 'ด่วน')) : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Referral panel --}}
                <div class="bg-white border border-brand-gray-border p-6 md:p-8" x-data="{ copied: false }">
                    <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-2">
                        {{ $isEn ? 'Refer a friend' : 'แนะนำเพื่อน' }}
                    </h2>
                    <p class="text-sm text-brand-gray-medium">
                        {{ $isEn
                            ? 'You earn '.config('chomin.referral.referrer_bonus_points').' points when a friend makes their first purchase. They get '.config('chomin.referral.referee_bonus_points').' points to start.'
                            : 'เมื่อเพื่อนสั่งซื้อครั้งแรก คุณจะได้ '.config('chomin.referral.referrer_bonus_points').' แต้ม เพื่อนได้ '.config('chomin.referral.referee_bonus_points').' แต้ม' }}
                    </p>

                    <div class="mt-5 flex flex-col sm:flex-row gap-2">
                        <input type="text" readonly value="{{ $shareUrl }}"
                               x-ref="shareLink"
                               class="flex-1 border border-brand-gray-border px-3 py-2 text-sm bg-brand-gray/40">
                        <button type="button"
                                @click="navigator.clipboard.writeText($refs.shareLink.value); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-brand-black text-white px-5 py-2 text-xs uppercase tracking-[0.14em]">
                            <span x-show="!copied">{{ $isEn ? 'Copy link' : 'คัดลอกลิงก์' }}</span>
                            <span x-show="copied" x-cloak>{{ $isEn ? 'Copied!' : 'คัดลอกแล้ว!' }}</span>
                        </button>
                    </div>

                    <div class="mt-3 flex gap-2 text-xs">
                        <a href="https://line.me/R/msg/text/?{{ urlencode($isEn ? 'Get '.config('chomin.referral.referee_bonus_points').' CHOMIN points: '.$shareUrl : 'รับ '.config('chomin.referral.referee_bonus_points').' แต้มจาก CHOMIN: '.$shareUrl) }}"
                           target="_blank" rel="noopener"
                           class="border border-brand-black px-3 py-1.5 uppercase tracking-[0.12em] hover:bg-brand-black hover:text-white">LINE</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                           target="_blank" rel="noopener"
                           class="border border-brand-black px-3 py-1.5 uppercase tracking-[0.12em] hover:bg-brand-black hover:text-white">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($isEn ? 'Get '.config('chomin.referral.referee_bonus_points').' CHOMIN points: ' : 'รับ '.config('chomin.referral.referee_bonus_points').' แต้มจาก CHOMIN: ') }}&url={{ urlencode($shareUrl) }}"
                           target="_blank" rel="noopener"
                           class="border border-brand-black px-3 py-1.5 uppercase tracking-[0.12em] hover:bg-brand-black hover:text-white">X</a>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="border border-brand-gray-border p-4">
                            <p class="text-[10px] uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Credited' : 'จ่ายแต้มแล้ว' }}</p>
                            <p class="mt-1 font-serif text-3xl">{{ $creditedCount }}</p>
                        </div>
                        <div class="border border-brand-gray-border p-4">
                            <p class="text-[10px] uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Pending' : 'รอใช้ครั้งแรก' }}</p>
                            <p class="mt-1 font-serif text-3xl">{{ $pendingCount }}</p>
                        </div>
                    </div>

                    @if($referrals->isNotEmpty())
                        <h3 class="mt-6 text-xs uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Your invitees' : 'เพื่อนที่ใช้ลิงก์' }}</h3>
                        <ul class="mt-2 divide-y divide-brand-gray-border border border-brand-gray-border">
                            @foreach($referrals as $ref)
                                <li class="flex items-center justify-between p-3 text-sm">
                                    <span>{{ $ref->name }}</span>
                                    <span class="text-xs {{ $ref->referral_credited_at ? 'text-brand-black' : 'text-brand-gray-medium' }}">
                                        {{ $ref->referral_credited_at
                                            ? ($isEn ? 'Credited '.$ref->referral_credited_at->isoFormat('LL') : 'จ่ายแล้ว '.$ref->referral_credited_at->isoFormat('LL'))
                                            : ($isEn ? 'Joined '.$ref->created_at->isoFormat('LL') : 'สมัคร '.$ref->created_at->isoFormat('LL')) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        @if($referrals->hasPages())
                            <div class="mt-4">{{ $referrals->links() }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
