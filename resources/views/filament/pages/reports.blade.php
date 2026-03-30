<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Date Range Filter --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white mb-4">ช่วงวันที่</h2>
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-700 dark:text-gray-200">จากวันที่</label>
                    <input type="date"
                           wire:model="from"
                           class="mt-1 block rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-700 dark:text-gray-200">ถึงวันที่</label>
                    <input type="date"
                           wire:model="to"
                           class="mt-1 block rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                </div>
                <button wire:click="export"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    Export Excel
                </button>
            </div>
        </div>

        {{-- Summary Stats --}}
        @php $summary = $this->summary; @endphp
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ออเดอร์สำเร็จ</p>
                <p class="mt-1 text-3xl font-bold text-gray-950 dark:text-white">{{ number_format($summary['total_orders']) }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ยอดขายรวม</p>
                <p class="mt-1 text-3xl font-bold text-success-600">฿{{ number_format($summary['total_revenue'], 2) }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ส่วนลดรวม</p>
                <p class="mt-1 text-3xl font-bold text-danger-600">฿{{ number_format($summary['total_discount'], 2) }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 p-6 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ยอดเฉลี่ยต่อออเดอร์</p>
                <p class="mt-1 text-3xl font-bold text-primary-600">฿{{ number_format($summary['avg_order_value'], 2) }}</p>
            </div>
        </div>

        {{-- Top Selling Products --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">สินค้าขายดี (Top 10)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">สินค้า</th>
                            <th class="px-6 py-3 text-right">จำนวนที่ขาย</th>
                            <th class="px-6 py-3 text-right">ยอดขาย</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($this->topProducts as $i => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-950 dark:text-white">{{ $item->product?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($item->total_qty) }}</td>
                            <td class="px-6 py-4 text-right text-success-600 font-medium">฿{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">ไม่มีข้อมูลในช่วงเวลาที่เลือก</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>
