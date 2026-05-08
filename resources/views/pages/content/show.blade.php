<x-layouts.shop :title="$title ?? $page->localized('title')" :description="$description ?? $page->localized('excerpt')">
    <section class="px-6 md:px-12 py-16 md:py-24">
        <div class="max-w-3xl mx-auto">
            <p class="text-xs tracking-[0.25em] uppercase text-brand-gray-light mb-6">
                CHOMIN
            </p>
            <h1 class="font-serif text-4xl md:text-6xl uppercase leading-none mb-8">
                {{ $page->localized('title') }}
            </h1>
            @if($page->localized('excerpt'))
                <p class="text-base text-brand-gray-dark leading-relaxed mb-10">
                    {{ $page->localized('excerpt') }}
                </p>
            @endif

            <div class="prose prose-neutral max-w-none text-brand-gray-dark leading-relaxed whitespace-pre-line">
                {{ $page->localized('body') }}
            </div>

            @if(in_array($page->slug, ['contact', 'careers', 'partnerships', 'wholesale'], true))
                <form method="POST" action="{{ route($page->slug === 'contact' ? 'contact.store' : $page->slug.'.store') }}" class="mt-12 space-y-5">
                    @csrf
                    <input type="text" name="company" class="hidden" tabindex="-1" autocomplete="off">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="name" value="{{ old('name') }}" required aria-label="{{ app()->getLocale() === 'en' ? 'Name' : 'ชื่อ' }}" placeholder="{{ app()->getLocale() === 'en' ? 'Name' : 'ชื่อ' }}" class="contact-field w-full min-h-[48px] rounded-none border-brand-gray-border px-4 py-3 text-sm focus:border-brand-black focus:ring-0">
                        <input name="email" value="{{ old('email') }}" required type="email" aria-label="Email" placeholder="Email" class="contact-field w-full min-h-[48px] rounded-none border-brand-gray-border px-4 py-3 text-sm focus:border-brand-black focus:ring-0">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="tel" name="phone" value="{{ old('phone') }}" aria-label="{{ app()->getLocale() === 'en' ? 'Phone' : 'เบอร์โทร' }}" placeholder="{{ app()->getLocale() === 'en' ? 'Phone' : 'เบอร์โทร' }}" class="contact-field w-full min-h-[48px] rounded-none border-brand-gray-border px-4 py-3 text-sm focus:border-brand-black focus:ring-0">
                        <input type="text" name="topic" value="{{ old('topic', $page->slug) }}" aria-label="{{ app()->getLocale() === 'en' ? 'Topic' : 'หัวข้อ' }}" placeholder="{{ app()->getLocale() === 'en' ? 'Topic' : 'หัวข้อ' }}" class="contact-field w-full min-h-[48px] rounded-none border-brand-gray-border px-4 py-3 text-sm focus:border-brand-black focus:ring-0">
                    </div>
                    <textarea name="message" required rows="5" aria-label="{{ app()->getLocale() === 'en' ? 'Message' : 'ข้อความ' }}" placeholder="{{ app()->getLocale() === 'en' ? 'Message' : 'ข้อความ' }}" class="contact-field w-full rounded-none border-brand-gray-border px-4 py-3 text-sm focus:border-brand-black focus:ring-0">{{ old('message') }}</textarea>
                    <button type="submit" class="contact-submit min-h-[48px] bg-brand-black px-8 py-3 text-xs uppercase tracking-[0.15em] text-white hover:bg-brand-gray-dark">
                        {{ app()->getLocale() === 'en' ? 'Send' : 'ส่งข้อความ' }}
                    </button>
                </form>
            @endif
        </div>
    </section>
</x-layouts.shop>
