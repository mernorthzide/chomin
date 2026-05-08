<x-layouts.shop :title="'500 | CHOMIN'">
    @php($errorLocale = in_array(request()->segment(1), config('chomin.locales.supported', ['th', 'en']), true) ? request()->segment(1) : app()->getLocale())
    <section class="min-h-[70vh] px-6 md:px-12 py-20 flex items-center">
        <div class="max-w-4xl mx-auto w-full">
            <p class="text-xs uppercase tracking-[0.24em] text-brand-gray-light mb-6">500</p>
            <h1 class="font-serif text-5xl md:text-7xl uppercase leading-none mb-8">
                {{ $errorLocale === 'en' ? 'Something went wrong' : 'ระบบขัดข้องชั่วคราว' }}
            </h1>
            <p class="text-sm md:text-base text-brand-gray-dark leading-relaxed max-w-xl mb-10">
                {{ $errorLocale === 'en'
                    ? 'Please try again shortly. If the issue continues, contact CHOMIN support.'
                    : 'ลองใหม่อีกครั้งในอีกสักครู่ หากยังพบปัญหา สามารถติดต่อทีม CHOMIN ได้' }}
            </p>
            <a href="{{ route('pages.contact') }}" class="bg-brand-black text-white px-8 py-3 text-xs uppercase tracking-[0.15em]">
                {{ $errorLocale === 'en' ? 'Contact us' : 'ติดต่อเรา' }}
            </a>
        </div>
    </section>
</x-layouts.shop>
