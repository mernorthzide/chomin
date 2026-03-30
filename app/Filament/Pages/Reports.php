<?php

namespace App\Filament\Pages;

use App\Exports\SalesReportExport;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Reports extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'รายงาน';

    protected static ?string $navigationLabel = 'รายงานยอดขาย';

    protected static ?string $title = 'รายงานยอดขาย';

    protected string $view = 'filament.pages.reports';

    public string $from = '';
    public string $to = '';

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->endOfMonth()->format('Y-m-d');
    }

    public function getSummaryProperty(): array
    {
        $query = Order::where('status', 'completed')
            ->whereBetween('completed_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59']);

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'total_discount' => $query->sum('discount'),
            'avg_order_value' => $query->count() > 0 ? $query->avg('total') : 0,
        ];
    }

    public function getTopProductsProperty(): \Illuminate\Support\Collection
    {
        return OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->whereHas('order', fn ($q) => $q
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            )
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
    }

    public function export(): BinaryFileResponse
    {
        $filename = 'sales-report-' . $this->from . '-to-' . $this->to . '.xlsx';
        return Excel::download(new SalesReportExport($this->from . ' 00:00:00', $this->to . ' 23:59:59'), $filename);
    }
}
