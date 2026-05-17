<x-layouts.shop :title="(app()->getLocale() === 'en' ? 'Gift Cards' : 'บัตรของขวัญ').' | CHOMIN'">
    @php $isEn = app()->getLocale() === 'en'; @endphp

    <section class="border-b border-brand-gray-border bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="px-6 md:px-12 py-12 md:py-20 flex flex-col justify-center">
                <p class="text-xs uppercase tracking-[0.2em] text-brand-gray-light">CHOMIN</p>
                <h1 class="mt-3 font-serif text-5xl md:text-7xl uppercase leading-none text-brand-black">
                    {{ $isEn ? 'Gift Cards' : 'บัตรของขวัญ' }}
                </h1>
                <p class="mt-5 text-sm md:text-base text-brand-gray-dark leading-relaxed max-w-md">
                    {{ $isEn
                        ? 'The shirt they choose, in their size, in the color they love. Cards never expire and can be combined at checkout.'
                        : 'ของขวัญที่ผู้รับได้เลือกเอง — ทั้งสี ทั้งไซส์ ใช้รวมหลายใบได้ ไม่มีวันหมดอายุ' }}
                </p>
            </div>
            <div class="bg-brand-gray aspect-square md:aspect-auto md:min-h-[420px] flex items-center justify-center p-8">
                <div class="bg-white p-8 md:p-12 shadow-sm w-full max-w-sm border border-brand-gray-border" x-data="{ amount: 1000 }">
                    <p class="text-[10px] uppercase tracking-[0.24em] text-brand-gray-light">CHO.MIN Gift Card</p>
                    <p class="mt-2 font-serif text-5xl md:text-6xl uppercase leading-none">
                        ฿<span x-text="amount.toLocaleString()"></span>
                    </p>
                    <p class="mt-6 text-xs uppercase tracking-[0.18em] text-brand-gray-medium">{{ $isEn ? 'Choose amount' : 'เลือกมูลค่า' }}</p>
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach($denominations as $amount)
                            <button type="button"
                                    @click="amount = {{ $amount }}; document.querySelector('[name=amount][value=\'{{ $amount }}\']')?.click()"
                                    class="border px-3 py-2 text-xs uppercase tracking-[0.14em]"
                                    :class="amount === {{ $amount }} ? 'border-brand-black bg-brand-black text-white' : 'border-brand-gray-border hover:border-brand-black'">
                                ฿{{ number_format($amount, 0) }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 md:px-12 py-12 md:py-16 bg-white">
        <div class="max-w-2xl mx-auto">
            @if(session('flash'))
                <div class="mb-6 border border-brand-black px-4 py-3 text-sm">
                    {{ session('flash')['message'] }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="font-serif text-3xl md:text-4xl uppercase">
                {{ $isEn ? 'Purchase a gift card' : 'สั่งซื้อบัตรของขวัญ' }}
            </h2>
            <p class="mt-2 text-sm text-brand-gray-medium">
                {{ $isEn ? 'We will send payment details and the card via email within 1 business day.' : 'ทีมงานจะส่งวิธีชำระเงินและบัตรของขวัญทางอีเมลภายใน 1 วันทำการ' }}
            </p>

            <form method="POST" action="{{ route('gift-cards.store') }}" class="mt-8 space-y-6">
                @csrf
                {{-- Honeypot --}}
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <label>Company<input type="text" name="company" tabindex="-1" autocomplete="off"></label>
                </div>

                <fieldset>
                    <legend class="text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-3">{{ $isEn ? 'Amount' : 'มูลค่า' }}</legend>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($denominations as $amount)
                            <label class="border border-brand-gray-border px-3 py-3 text-center cursor-pointer text-sm has-[:checked]:border-brand-black has-[:checked]:bg-brand-black has-[:checked]:text-white">
                                <input type="radio" name="amount" value="{{ $amount }}" {{ old('amount', 1000) == $amount ? 'checked' : '' }} class="sr-only">
                                ฿{{ number_format($amount, 0) }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Your name' : 'ชื่อผู้สั่งซื้อ' }}</label>
                        <input type="text" name="buyer_name" required maxlength="120" value="{{ old('buyer_name') }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Your email' : 'อีเมลผู้สั่งซื้อ' }}</label>
                        <input type="email" name="buyer_email" required maxlength="160" value="{{ old('buyer_email') }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Your phone (optional)' : 'เบอร์ติดต่อ (ไม่บังคับ)' }}</label>
                        <input type="text" name="buyer_phone" maxlength="40" value="{{ old('buyer_phone') }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="border-t border-brand-gray-border pt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Recipient name' : 'ชื่อผู้รับ' }}</label>
                        <input type="text" name="recipient_name" required maxlength="120" value="{{ old('recipient_name') }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Recipient email (optional)' : 'อีเมลผู้รับ (ไม่บังคับ)' }}</label>
                        <input type="email" name="recipient_email" maxlength="160" value="{{ old('recipient_email') }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Message (optional)' : 'ข้อความ (ไม่บังคับ)' }}</label>
                        <textarea name="message" rows="3" maxlength="500"
                                  class="w-full border border-brand-gray-border px-3 py-2 text-sm">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs uppercase tracking-[0.14em] text-brand-gray-light mb-1">{{ $isEn ? 'Deliver on (optional)' : 'ส่งวันที่ (ไม่บังคับ)' }}</label>
                        <input type="date" name="deliver_on" value="{{ old('deliver_on') }}"
                               min="{{ now()->toDateString() }}"
                               class="w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                </div>

                <button type="submit" class="bg-brand-black px-6 py-3 text-xs uppercase tracking-[0.16em] text-white">
                    {{ $isEn ? 'Submit order' : 'ส่งคำสั่งซื้อ' }}
                </button>
            </form>
        </div>
    </section>
</x-layouts.shop>
