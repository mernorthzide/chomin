<x-layouts.shop>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
         x-data="{ showAddForm: false, editId: null }">

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

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black">
                            ที่อยู่จัดส่ง
                        </h2>
                        <button @click="showAddForm = !showAddForm"
                                class="text-xs font-medium tracking-[0.15em] uppercase px-4 py-2 border border-brand-black text-brand-black hover:bg-brand-black hover:text-white transition-colors duration-200">
                            + เพิ่มที่อยู่
                        </button>
                    </div>

                    {{-- Add Address Form --}}
                    <div x-show="showAddForm" x-collapse class="mb-6">
                        <div class="border border-brand-gray-border p-6 bg-brand-gray">
                            <h3 class="text-xs font-medium tracking-widest uppercase text-brand-black mb-4">
                                เพิ่มที่อยู่ใหม่
                            </h3>
                            <form method="POST" action="{{ route('addresses.store') }}">
                                @csrf
                                @include('pages.profile._address-form')
                                <div class="mt-4 flex gap-3">
                                    <button type="submit"
                                            class="px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-brown transition-colors duration-300">
                                        บันทึก
                                    </button>
                                    <button type="button" @click="showAddForm = false"
                                            class="px-6 py-2 border border-brand-gray-border text-xs font-medium tracking-[0.15em] uppercase hover:bg-white transition-colors duration-200">
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Address List --}}
                    @if($addresses->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-brand-gray-medium text-sm">ยังไม่มีที่อยู่จัดส่ง</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $address)
                                <div class="border border-brand-gray-border p-5"
                                     x-data="{ editing: false }">

                                    {{-- View Mode --}}
                                    <div x-show="!editing">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <p class="text-sm font-medium text-brand-black">{{ $address->name }}</p>
                                                    @if($address->is_default)
                                                        <span class="text-xs px-2 py-0.5 bg-brand-black text-white font-medium tracking-wide">
                                                            ค่าเริ่มต้น
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-brand-gray-dark">{{ $address->phone }}</p>
                                                <p class="text-sm text-brand-gray-dark mt-1">{{ $address->full_address }}</p>
                                            </div>
                                            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                                <button @click="editing = true"
                                                        class="text-xs text-brand-gray-medium hover:text-brand-black underline transition-colors duration-150">
                                                    แก้ไข
                                                </button>
                                                <form method="POST" action="{{ route('addresses.destroy', $address) }}"
                                                      onsubmit="return confirm('ยืนยันการลบที่อยู่นี้?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-xs text-red-400 hover:text-red-600 underline transition-colors duration-150">
                                                        ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit Mode --}}
                                    <div x-show="editing" x-collapse>
                                        <form method="POST" action="{{ route('addresses.update', $address) }}">
                                            @csrf
                                            @method('PATCH')
                                            @include('pages.profile._address-form', ['address' => $address])
                                            <div class="mt-4 flex gap-3">
                                                <button type="submit"
                                                        class="px-6 py-2 bg-brand-black text-white text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-brown transition-colors duration-300">
                                                    บันทึก
                                                </button>
                                                <button type="button" @click="editing = false"
                                                        class="px-6 py-2 border border-brand-gray-border text-xs font-medium tracking-[0.15em] uppercase hover:bg-brand-gray transition-colors duration-200">
                                                    ยกเลิก
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
