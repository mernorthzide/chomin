@props(['product'])

@php
    $reviews = $product->approvedReviews()->limit(20)->get();
    $avg = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : null;
    $distribution = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($star) => [
        $star => $reviews->where('rating', $star)->count(),
    ]);
    $total = $reviews->count();
    $isEn = app()->getLocale() === 'en';
    $photoGallery = $reviews
        ->flatMap(fn ($r) => collect($r->photos ?? [])->map(fn ($p) => ['path' => $p, 'review' => $r]))
        ->take(8);
@endphp

<section id="reviews" class="border-t border-brand-gray-border bg-white py-12 md:py-16">
    <div class="px-6 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">

            {{-- Summary --}}
            <div class="md:col-span-1">
                <p class="text-xs uppercase tracking-[0.18em] text-brand-gray-light">{{ $isEn ? 'Reviews' : 'รีวิว' }}</p>
                <h2 class="mt-2 font-serif text-3xl uppercase md:text-4xl">
                    {{ $isEn ? 'Customer voices' : 'เสียงจากลูกค้า' }}
                </h2>

                @if($total > 0)
                    <div class="mt-5 flex items-baseline gap-3">
                        <span class="text-5xl font-serif">{{ $avg }}</span>
                        <span class="text-sm text-brand-gray-medium">/ 5</span>
                    </div>
                    <div class="mt-1 flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($avg) ? 'fill-brand-black' : 'fill-brand-gray-border' }}" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.959a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 0 00-.363 1.118l1.287 3.959c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.959a1 1 0 00-.364-1.118L2.05 9.386c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.959z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-2 text-xs text-brand-gray-medium">{{ $total }} {{ $isEn ? 'reviews' : 'รีวิว' }}</p>

                    <div class="mt-6 space-y-1">
                        @foreach($distribution as $star => $count)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="w-6">{{ $star }}★</span>
                                <div class="h-1.5 flex-1 bg-brand-gray-border">
                                    <div class="h-full bg-brand-black" style="width: {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%"></div>
                                </div>
                                <span class="w-6 text-right text-brand-gray-medium">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-5 text-sm text-brand-gray-medium">
                        {{ $isEn ? 'No reviews yet — be the first to share your experience.' : 'ยังไม่มีรีวิว — ร่วมเป็นคนแรกที่แชร์ประสบการณ์' }}
                    </p>
                @endif

                <button type="button"
                        @click="$dispatch('open-review-form')"
                        class="mt-6 inline-flex min-h-11 items-center justify-center border border-brand-black px-5 text-xs uppercase tracking-[0.16em] hover:bg-brand-black hover:text-white">
                    {{ $isEn ? 'Write a review' : 'เขียนรีวิว' }}
                </button>
            </div>

            {{-- Review list --}}
            <div class="md:col-span-2">
                {{-- UGC photo gallery (above reviews) --}}
                @if($photoGallery->isNotEmpty())
                    <div x-data="{ lightbox: null }" class="mb-6">
                        <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light mb-3">
                            {{ $isEn ? 'From real customers' : 'รูปจริงจากลูกค้า' }}
                        </p>
                        <div class="grid grid-cols-4 gap-2 md:grid-cols-8">
                            @foreach($photoGallery as $photo)
                                <button type="button"
                                        @click="lightbox = '{{ Storage::url($photo['path']) }}'"
                                        class="aspect-square overflow-hidden bg-brand-gray hover:opacity-80">
                                    <img src="{{ Storage::url($photo['path']) }}"
                                         alt="Customer photo"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </button>
                            @endforeach
                        </div>
                        <div x-show="lightbox" x-cloak
                             @click="lightbox = null"
                             @keydown.escape.window="lightbox = null"
                             class="fixed inset-0 z-[80] flex items-center justify-center bg-black/90 p-6"
                             x-transition.opacity>
                            <img :src="lightbox" alt="Customer photo" class="max-h-[90vh] max-w-[90vw] object-contain">
                            <button type="button" @click.stop="lightbox = null"
                                    class="absolute right-4 top-4 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @forelse($reviews as $review)
                    <article class="border-b border-brand-gray-border py-5 first:pt-0">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'fill-brand-black' : 'fill-brand-gray-border' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.959a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.449a1 1 0 00-.363 1.118l1.287 3.959c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.959a1 1 0 00-.364-1.118L2.05 9.386c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.959z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @if($review->title)
                                    <h3 class="mt-2 text-sm font-medium uppercase tracking-[0.08em]">{{ $review->title }}</h3>
                                @endif
                            </div>
                            @if($review->is_verified_purchase)
                                <span class="text-[10px] uppercase tracking-[0.14em] text-brand-black border border-brand-black px-2 py-0.5">
                                    {{ $isEn ? 'Verified' : 'ยืนยันการซื้อ' }}
                                </span>
                            @endif
                        </header>
                        @if($review->body)
                            <p class="mt-3 text-sm leading-relaxed text-brand-gray-dark whitespace-pre-line">{{ $review->body }}</p>
                        @endif
                        @if(!empty($review->photos))
                            <div class="mt-3 flex gap-2 flex-wrap">
                                @foreach($review->photos as $photo)
                                    <a href="{{ Storage::url($photo) }}" target="_blank" rel="noopener"
                                       class="w-20 h-20 overflow-hidden bg-brand-gray block">
                                        <img src="{{ Storage::url($photo) }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <footer class="mt-3 text-[11px] uppercase tracking-[0.12em] text-brand-gray-light">
                            {{ $review->name ?: ($isEn ? 'Customer' : 'ลูกค้า') }} · {{ $review->created_at->isoFormat('LL') }}
                        </footer>
                        @if($review->admin_response)
                            <div class="mt-3 ml-4 border-l-2 border-brand-black pl-4 py-2 bg-brand-gray/30">
                                <p class="text-[11px] uppercase tracking-[0.12em] text-brand-gray-light mb-1">{{ $isEn ? 'CHOMIN replied' : 'CHOMIN ตอบ' }}</p>
                                <p class="text-sm text-brand-gray-dark">{{ $review->admin_response }}</p>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-brand-gray-medium">{{ $isEn ? 'Be the first to review.' : 'มาเป็นคนแรกที่รีวิว' }}</p>
                @endforelse
            </div>
        </div>

        {{-- Review form --}}
        <div x-data="{ open: false }" @open-review-form.window="open = true" class="mt-8">
            <div x-show="open" x-cloak x-transition class="border border-brand-gray-border bg-white p-6 md:p-8">
                <form method="POST" action="{{ route('products.reviews.store', $product->slug) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Rating' : 'ให้คะแนน' }}</label>
                        <div class="mt-2 flex gap-2" x-data="{ rating: 5 }">
                            <template x-for="n in 5" :key="n">
                                <button type="button" @click="rating = n" class="text-3xl" :class="n <= rating ? 'text-brand-black' : 'text-brand-gray-border'">★</button>
                            </template>
                            <input type="hidden" name="rating" x-model="rating">
                        </div>
                    </div>
                    @guest
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Name' : 'ชื่อ' }}</label>
                            <input type="text" name="name" required class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Email' : 'อีเมล' }}</label>
                            <input type="email" name="email" required class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm">
                        </div>
                    </div>
                    @endguest
                    <div>
                        <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Title (optional)' : 'หัวข้อ (ถ้ามี)' }}</label>
                        <input type="text" name="title" maxlength="120" class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Your review' : 'รายละเอียดรีวิว' }}</label>
                        <textarea name="body" rows="4" maxlength="2000" class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">
                            {{ $isEn ? 'Photos (optional, up to 4)' : 'รูปภาพ (ถ้ามี ไม่เกิน 4 รูป)' }}
                        </label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                               class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-xs file:mr-3 file:border-0 file:bg-brand-black file:text-white file:px-3 file:py-1 file:text-[11px] file:uppercase">
                        <p class="mt-1 text-[10px] text-brand-gray-light">{{ $isEn ? 'JPG, PNG or WEBP, max 4MB each.' : 'รับไฟล์ JPG/PNG/WEBP ขนาดไม่เกิน 4MB ต่อรูป' }}</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="bg-brand-black px-5 py-2.5 text-xs uppercase tracking-[0.16em] text-white">
                            {{ $isEn ? 'Submit review' : 'ส่งรีวิว' }}
                        </button>
                        <button type="button" @click="open = false" class="border border-brand-black px-5 py-2.5 text-xs uppercase tracking-[0.16em]">
                            {{ $isEn ? 'Cancel' : 'ยกเลิก' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
