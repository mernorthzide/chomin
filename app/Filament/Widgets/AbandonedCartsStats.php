<?php

namespace App\Filament\Widgets;

use App\Models\AbandonedCart;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AbandonedCartsStats extends StatsOverviewWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $stats = AbandonedCart::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('COUNT(*) as total, COUNT(recovered_at) as recovered, SUM(CASE WHEN recovered_at IS NULL THEN total ELSE 0 END) as pending_value')
            ->first();

        $total30 = (int) ($stats->total ?? 0);
        $recovered30 = (int) ($stats->recovered ?? 0);
        $pendingValue = (float) ($stats->pending_value ?? 0);
        $recoveryRate = $total30 > 0
            ? round($recovered30 / $total30 * 100, 1)
            : 0;

        return [
            Stat::make('ตะกร้าที่ทิ้ง (30 วัน)', $total30)
                ->icon('heroicon-o-shopping-cart')
                ->color('warning'),
            Stat::make('กู้คืนแล้ว (30 วัน)', $recovered30)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description("อัตราการกู้คืน {$recoveryRate}%"),
            Stat::make('มูลค่าที่ทิ้งรวม (30 วัน)', '฿'.number_format($pendingValue))
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
        ];
    }
}
