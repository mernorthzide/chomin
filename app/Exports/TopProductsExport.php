<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $from, private string $to) {}

    public function collection(): Collection
    {
        return OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->whereHas('order', fn ($query) => $query
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$this->from, $this->to])
            )
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();
    }

    public function headings(): array
    {
        return ['สินค้า', 'จำนวนที่ขาย', 'ยอดขาย'];
    }

    public function map($row): array
    {
        return [
            $row->product?->name ?? '-',
            (int) $row->total_qty,
            (float) $row->total_revenue,
        ];
    }
}
