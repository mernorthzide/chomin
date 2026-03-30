<?php
namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private string $from, private string $to) {}

    public function query()
    {
        return Order::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$this->from, $this->to])
            ->with('user')
            ->orderByDesc('completed_at');
    }

    public function headings(): array
    {
        return ['เลขออเดอร์', 'ลูกค้า', 'ยอดรวม', 'ส่วนลด', 'ค่าส่ง', 'สุทธิ', 'วันที่'];
    }

    public function map($order): array
    {
        return [
            $order->order_number, $order->user->name, $order->subtotal,
            $order->discount, $order->shipping_fee, $order->total,
            $order->completed_at?->format('d/m/Y H:i'),
        ];
    }
}
