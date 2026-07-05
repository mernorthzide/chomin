<x-layouts.shop :title="app()->getLocale() === 'en' ? 'Loyalty Points' : 'แต้มสะสม'" :noindex="true">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        <p class="text-xs font-medium tracking-widest uppercase text-brand-gray-light mb-2">
            {{ app()->getLocale() === 'en' ? 'My Account' : 'บัญชีของฉัน' }}
        </p>
        <h1 class="text-2xl md:text-3xl font-medium text-brand-black tracking-widest uppercase mb-8">
            {{ app()->getLocale() === 'en' ? 'Loyalty Points' : 'แต้มสะสม' }}
        </h1>

        <div class="lg:grid lg:grid-cols-4 lg:gap-8">

            {{-- Sidebar --}}
            <div class="lg:col-span-1 mb-6 lg:mb-0">
                @include('pages.profile._sidebar')
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Points Balance --}}
                <div class="bg-white border border-brand-gray-border p-8 text-center">
                    <p class="text-xs font-medium tracking-widest uppercase text-brand-gray-medium mb-3">
                        แต้มสะสมของคุณ
                    </p>
                    <p class="text-5xl font-medium text-brand-black">
                        {{ number_format(auth()->user()->points) }}
                    </p>
                    <p class="text-sm text-brand-gray-medium mt-2">แต้ม</p>
                    <p class="text-xs text-brand-gray-medium mt-4">
                        1 แต้ม = ส่วนลด ฿1
                    </p>
                </div>

                {{-- Transaction History --}}
                <div class="bg-white border border-brand-gray-border p-6 md:p-8">
                    <h2 class="text-sm font-medium tracking-widest uppercase text-brand-black mb-6">
                        ประวัติแต้ม
                    </h2>

                    @if($transactions->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-brand-gray-medium text-sm">ยังไม่มีประวัติการใช้แต้ม</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-brand-gray-border">
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">วันที่</th>
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">ประเภท</th>
                                        <th class="text-left text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3 pr-4">รายละเอียด</th>
                                        <th class="text-right text-xs font-medium tracking-widest uppercase text-brand-gray-medium pb-3">แต้ม</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-gray-border">
                                    @foreach($transactions as $tx)
                                        <tr>
                                            <td class="py-3 pr-4 text-brand-gray-dark text-xs">
                                                {{ $tx->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                @if($tx->type === 'earn')
                                                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-brand-success/10 text-brand-success">
                                                        รับแต้ม
                                                    </span>
                                                @elseif($tx->type === 'redeem')
                                                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-brand-danger/10 text-brand-danger">
                                                        ใช้แต้ม
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 text-xs font-medium bg-brand-gray text-brand-gray-dark border border-brand-gray-border">
                                                        {{ $tx->type }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 pr-4 text-brand-gray-dark">
                                                {{ $tx->description ?? '-' }}
                                            </td>
                                            <td class="py-3 text-right font-medium
                                                {{ $tx->points >= 0 ? 'text-brand-success' : 'text-brand-danger' }}">
                                                {{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($transactions->hasPages())
                            <div class="mt-6">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @endif

                </div>

            </div>
        </div>

    </div>

</x-layouts.shop>
