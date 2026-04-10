<x-layouts.shop>

    <!-- Page Header -->
    <section class="bg-brand-black py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="font-serif text-4xl md:text-5xl font-normal text-white uppercase tracking-widest">
                {{ $aboutTitle }}
            </h1>
        </div>
    </section>

    <!-- About Image (if available) -->
    @if($aboutImage)
        <div class="w-full aspect-[16/6] overflow-hidden bg-brand-gray">
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($aboutImage) }}"
                alt="{{ $aboutTitle }}"
                class="w-full h-full object-cover">
        </div>
    @endif

    <!-- About Content -->
    <section class="bg-white py-16 md:py-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($aboutContent)
                <div class="prose prose-lg max-w-none
                            prose-headings:font-serif prose-headings:font-normal prose-headings:tracking-wider prose-headings:text-brand-black
                            prose-p:text-brand-gray-dark prose-p:leading-relaxed prose-p:text-base
                            prose-a:text-brand-brown prose-a:no-underline hover:prose-a:underline
                            prose-strong:font-semibold prose-strong:text-brand-black
                            prose-hr:border-brand-gray-border">
                    {!! $aboutContent !!}
                </div>
            @else
                <div class="text-center py-10">
                    <x-brand-logo variant="dark" class="h-12 mx-auto mb-6" />
                    <div class="w-12 h-px bg-brand-gray-border mx-auto mb-6"></div>
                    <p class="text-brand-gray-medium text-base leading-relaxed max-w-xl mx-auto mb-4">
                        CHO.MIN คือแบรนด์ที่เชื่อว่า สไตล์ ไม่ควรถูกจำกัด เราออกแบบเชิ้ตให้คุณเป็นคนกำหนดเอง
                    </p>
                    <p class="text-brand-gray-medium text-base leading-relaxed max-w-xl mx-auto mb-4">
                        เลือกได้มากถึง 50 สี ตั้งแต่โทนมินิมอล คลาสสิก ไปจนถึงเฉดจัดจ้านสายแฟชั่น
                        โครงสร้างทรงสวยที่ออกแบบให้ใส่ได้ทั้งหญิงและชาย เนื้อผ้าคุณภาพสัมผัสนุ่ม ใส่สบายและดูแพงอย่างเป็นธรรมชาติ
                    </p>
                    <p class="text-brand-gray-medium text-base leading-relaxed max-w-xl mx-auto">
                        ดีไซน์เรียบ แต่ซ่อนรายละเอียดที่ทำให้แตกต่างอย่างมีชั้นเชิง
                    </p>
                </div>
            @endif
        </div>
    </section>

    <!-- Brand Values Section -->
    <section class="bg-brand-gray py-16 md:py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-16 text-center">
                <div>
                    <div class="w-10 h-px bg-brand-black mx-auto mb-6"></div>
                    <h3 class="font-serif text-lg font-normal text-brand-black tracking-wider mb-3">50+ สี</h3>
                    <p class="text-sm text-brand-gray-medium leading-relaxed">
                        เลือกได้มากถึง 50 สี ตั้งแต่โทนมินิมอล คลาสสิก ไปจนถึงเฉดจัดจ้านสายแฟชั่น
                    </p>
                </div>
                <div>
                    <div class="w-10 h-px bg-brand-black mx-auto mb-6"></div>
                    <h3 class="font-serif text-lg font-normal text-brand-black tracking-wider mb-3">Unisex</h3>
                    <p class="text-sm text-brand-gray-medium leading-relaxed">
                        โครงสร้างทรงสวยที่ออกแบบให้ใส่ได้ทั้งหญิงและชาย ไซส์ XS ถึง 6XL
                    </p>
                </div>
                <div>
                    <div class="w-10 h-px bg-brand-black mx-auto mb-6"></div>
                    <h3 class="font-serif text-lg font-normal text-brand-black tracking-wider mb-3">Premium Quality</h3>
                    <p class="text-sm text-brand-gray-medium leading-relaxed">
                        เนื้อผ้าคุณภาพสัมผัสนุ่ม ใส่สบายและดูแพงอย่างเป็นธรรมชาติ
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-brand-black py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-serif text-2xl md:text-3xl font-normal text-white tracking-widest mb-6">
                ค้นพบสินค้าของเรา
            </h2>
            <a href="{{ route('shop.index') }}"
               class="inline-block px-10 py-4 border border-white text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-white hover:text-brand-black transition-all duration-300">
                ชมสินค้าทั้งหมด
            </a>
        </div>
    </section>

</x-layouts.shop>
