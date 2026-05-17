@php $isEn = app()->getLocale() === 'en'; @endphp

<div x-data="sizeRecommender()" class="mt-3">
    <button type="button" @click="open = !open"
            class="text-xs uppercase tracking-[0.14em] underline-offset-4 underline hover:opacity-60">
        {{ $isEn ? 'Find my size' : 'หาไซส์ที่ใช่' }}
    </button>

    <div x-show="open" x-cloak x-transition class="mt-3 border border-brand-gray-border p-5 bg-white">
        <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'Quick size finder' : 'หาไซส์เร็ว' }}</p>
        <h3 class="mt-2 font-serif text-xl uppercase">{{ $isEn ? 'Your measurements' : 'วัดตัวคุณ' }}</h3>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <label class="block">
                <span class="text-[11px] uppercase tracking-[0.12em] text-brand-gray-medium">{{ $isEn ? 'Height (cm)' : 'ส่วนสูง (ซม.)' }}</span>
                <input type="number" min="140" max="210" x-model.number="heightCm" placeholder="170"
                       class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm">
            </label>
            <label class="block">
                <span class="text-[11px] uppercase tracking-[0.12em] text-brand-gray-medium">{{ $isEn ? 'Weight (kg)' : 'น้ำหนัก (กก.)' }}</span>
                <input type="number" min="35" max="160" x-model.number="weightKg" placeholder="65"
                       class="mt-1 w-full border border-brand-gray-border px-3 py-2 text-sm">
            </label>
        </div>

        <div class="mt-3">
            <span class="text-[11px] uppercase tracking-[0.12em] text-brand-gray-medium">{{ $isEn ? 'Fit preference' : 'รูปทรงที่ชอบ' }}</span>
            <div class="mt-1 flex gap-2">
                <template x-for="opt in ['slim','regular','relaxed']" :key="opt">
                    <button type="button" @click="fit = opt"
                            class="flex-1 border py-2 text-xs uppercase tracking-[0.12em]"
                            :class="fit === opt ? 'border-brand-black bg-brand-black text-white' : 'border-brand-gray-border'"
                            x-text="opt"></button>
                </template>
            </div>
        </div>

        <button type="button" @click="calculate()"
                class="mt-4 w-full bg-brand-black px-4 py-2.5 text-xs uppercase tracking-[0.14em] text-white">
            {{ $isEn ? 'Show my size' : 'แสดงไซส์ของคุณ' }}
        </button>

        <template x-if="recommendedSize">
            <div class="mt-4 border-t border-brand-gray-border pt-4 text-center">
                <p class="text-[11px] uppercase tracking-[0.14em] text-brand-gray-light">{{ $isEn ? 'We recommend' : 'แนะนำไซส์' }}</p>
                <p class="mt-2 font-serif text-4xl uppercase" x-text="recommendedSize"></p>
                <p class="mt-2 text-[11px] text-brand-gray-medium" x-text="explanation"></p>
            </div>
        </template>
        <p class="mt-3 text-[10px] uppercase tracking-[0.12em] text-brand-gray-light text-center">
            <a href="{{ route('pages.size-guide') }}" class="underline">{{ $isEn ? 'See full size chart' : 'ดูตารางไซส์ทั้งหมด' }}</a>
        </p>
    </div>
</div>

<script>
function sizeRecommender() {
    return {
        open: false,
        heightCm: null,
        weightKg: null,
        fit: 'regular',
        recommendedSize: null,
        explanation: '',
        calculate() {
            const h = this.heightCm, w = this.weightKg;
            if (!h || !w || h < 140 || h > 210 || w < 35 || w > 160) {
                this.recommendedSize = null;
                this.explanation = '{{ $isEn ? "Please enter valid height & weight" : "กรุณากรอกข้อมูลให้ถูกต้อง" }}';
                return;
            }
            const bmi = w / Math.pow(h / 100, 2);
            // Base size from BMI
            let baseIdx;
            if (bmi < 18.5) baseIdx = 0;       // XS
            else if (bmi < 21) baseIdx = 1;    // S
            else if (bmi < 24) baseIdx = 2;    // M
            else if (bmi < 27) baseIdx = 3;    // L
            else if (bmi < 30) baseIdx = 4;    // XL
            else if (bmi < 33) baseIdx = 5;    // 2XL
            else if (bmi < 36) baseIdx = 6;    // 3XL
            else if (bmi < 39) baseIdx = 7;    // 4XL
            else if (bmi < 42) baseIdx = 8;    // 5XL
            else baseIdx = 9;                  // 6XL

            // Adjust by fit preference
            if (this.fit === 'slim') baseIdx = Math.max(0, baseIdx - 1);
            if (this.fit === 'relaxed') baseIdx = Math.min(9, baseIdx + 1);

            // Adjust by height extremes (very tall → +1, very short → -1)
            if (h >= 185) baseIdx = Math.min(9, baseIdx + 1);
            if (h <= 155) baseIdx = Math.max(0, baseIdx - 1);

            const sizes = ['XS','S','M','L','XL','2XL','3XL','4XL','5XL','6XL'];
            this.recommendedSize = sizes[baseIdx];
            const fitMap = { slim: '{{ $isEn ? "slim" : "ทรงพอดี" }}', regular: '{{ $isEn ? "regular" : "ทรงปกติ" }}', relaxed: '{{ $isEn ? "relaxed" : "ทรงหลวม" }}' };
            this.explanation = '{{ $isEn ? "Based on BMI " : "คำนวณจาก BMI " }}' + bmi.toFixed(1) + ' · ' + fitMap[this.fit];
        }
    };
}
</script>
