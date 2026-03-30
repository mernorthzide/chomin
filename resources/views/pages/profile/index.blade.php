<x-layouts.shop>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            บัญชีของฉัน
        </h1>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Sidebar --}}
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3">

                @if(session('success'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white border border-brand-gray-border p-6 md:p-8">

                    <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-6">
                        ข้อมูลส่วนตัว
                    </h2>

                    {{-- Points Summary --}}
                    <div class="mb-8 p-4 bg-brand-gray flex items-center justify-between">
                        <div>
                            <p class="text-xs text-brand-gray-medium tracking-wide">แต้มสะสมของคุณ</p>
                            <p class="text-2xl font-medium text-brand-black mt-1">
                                {{ number_format(auth()->user()->points) }}
                                <span class="text-sm font-normal text-brand-gray-medium">แต้ม</span>
                            </p>
                        </div>
                        <a href="{{ route('profile.points') }}"
                           class="text-xs text-brand-gray-dark hover:text-brand-black underline tracking-wide">
                            ดูประวัติแต้ม &rarr;
                        </a>
                    </div>

                    {{-- Edit Form --}}
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-5">

                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                                    ชื่อ - นามสกุล
                                </label>
                                <input type="text" id="name" name="name"
                                       value="{{ old('name', auth()->user()->name) }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white transition-colors duration-200"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email (read-only) --}}
                            <div>
                                <label class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                                    อีเมล
                                </label>
                                <input type="email" value="{{ auth()->user()->email }}"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm text-brand-gray-medium bg-brand-gray cursor-not-allowed"
                                       readonly>
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-xs font-medium tracking-widest uppercase text-brand-gray-dark mb-2">
                                    เบอร์โทรศัพท์
                                </label>
                                <input type="tel" id="phone" name="phone"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       placeholder="08X-XXX-XXXX"
                                       class="w-full border border-brand-gray-border px-4 py-3 text-sm text-brand-black focus:outline-none focus:border-brand-black bg-white transition-colors duration-200">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-8 flex items-center gap-4">
                            <button type="submit"
                                    class="px-8 py-3 bg-brand-black text-white text-xs font-medium tracking-[0.2em] uppercase hover:bg-brand-brown transition-colors duration-300">
                                บันทึกข้อมูล
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
