<x-filament-panels::page>
    <div style="display:flex; flex-direction:column; gap:1.5rem;">

        {{-- Date Range Filter --}}
        <x-filament::section>
            <div style="display:flex; flex-wrap:wrap; align-items:flex-end; gap:1rem;">
                <div style="flex:1; min-width:160px;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.375rem;">จากวันที่</label>
                    <input type="date" wire:model.live="from"
                           style="width:100%; padding:0.5rem 0.75rem; font-size:0.875rem; border:1px solid #d1d5db; border-radius:0.5rem; outline:none;">
                </div>
                <div style="flex:1; min-width:160px;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.375rem;">ถึงวันที่</label>
                    <input type="date" wire:model.live="to"
                           style="width:100%; padding:0.5rem 0.75rem; font-size:0.875rem; border:1px solid #d1d5db; border-radius:0.5rem; outline:none;">
                </div>
                <div style="flex-shrink:0;">
                    <x-filament::button wire:click="export" icon="heroicon-o-arrow-down-tray" size="lg">
                        Export Excel
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Summary Stats --}}
        @php $summary = $this->summary; @endphp
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:1.25rem;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:0.75rem; padding:1.25rem 1.5rem; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                    <div style="width:2.5rem; height:2.5rem; background:#dbeafe; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.25rem; height:1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                    <span style="font-size:0.8125rem; color:#6b7280; font-weight:500;">ออเดอร์สำเร็จ</span>
                </div>
                <div style="font-size:1.875rem; font-weight:700; color:#111827;">{{ number_format($summary['total_orders']) }}</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:0.75rem; padding:1.25rem 1.5rem; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                    <div style="width:2.5rem; height:2.5rem; background:#dcfce7; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#16a34a" style="width:1.25rem; height:1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span style="font-size:0.8125rem; color:#6b7280; font-weight:500;">ยอดขายรวม</span>
                </div>
                <div style="font-size:1.875rem; font-weight:700; color:#16a34a;">฿{{ number_format($summary['total_revenue'], 2) }}</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:0.75rem; padding:1.25rem 1.5rem; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                    <div style="width:2.5rem; height:2.5rem; background:#fee2e2; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626" style="width:1.25rem; height:1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                    </div>
                    <span style="font-size:0.8125rem; color:#6b7280; font-weight:500;">ส่วนลดรวม</span>
                </div>
                <div style="font-size:1.875rem; font-weight:700; color:#dc2626;">฿{{ number_format($summary['total_discount'], 2) }}</div>
            </div>
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:0.75rem; padding:1.25rem 1.5rem; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                    <div style="width:2.5rem; height:2.5rem; background:#fef3c7; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.25rem; height:1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <span style="font-size:0.8125rem; color:#6b7280; font-weight:500;">ยอดเฉลี่ยต่อออเดอร์</span>
                </div>
                <div style="font-size:1.875rem; font-weight:700; color:#d97706;">฿{{ number_format($summary['avg_order_value'], 2) }}</div>
            </div>
        </div>

        {{-- Top Selling Products --}}
        <x-filament::section heading="สินค้าขายดี (Top 10)">
            <div style="overflow-x:auto;">
                <table style="width:100%; font-size:0.875rem; text-align:left; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid #e5e7eb;">
                            <th style="padding:0.75rem 1rem; font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">#</th>
                            <th style="padding:0.75rem 1rem; font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">สินค้า</th>
                            <th style="padding:0.75rem 1rem; font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; text-align:right;">จำนวนที่ขาย</th>
                            <th style="padding:0.75rem 1rem; font-size:0.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; text-align:right;">ยอดขาย</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->topProducts as $i => $item)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:0.875rem 1rem; color:#9ca3af; font-weight:500;">{{ $i + 1 }}</td>
                            <td style="padding:0.875rem 1rem; font-weight:500; color:#111827;">{{ $item->product?->name ?? '-' }}</td>
                            <td style="padding:0.875rem 1rem; text-align:right;">{{ number_format($item->total_qty) }}</td>
                            <td style="padding:0.875rem 1rem; text-align:right; color:#16a34a; font-weight:600;">฿{{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding:2.5rem 1rem; text-align:center; color:#9ca3af;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="#d1d5db" style="width:3rem; height:3rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <span>ไม่มีข้อมูลในช่วงเวลาที่เลือก</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
