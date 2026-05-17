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
        $total30 = AbandonedCart::where('created_at', '>=', now()->subDays(30))->count();

        $recovered30 = AbandonedCart::where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('recovered_at')
            ->count();

        $recoveryRate = $total30 > 0
            ? round($recovered30 / $total30 * 100, 1)
            : 0;

        $pendingValue = AbandonedCart::where('created_at', '>=', now()->subDays(30))
            ->whereNull('recovered_at')
            ->sum('total');

        return [
            Stat::make('ตะกร้าที่ทิ้ง (30 วัน)', $total30)
                ->icon('heroicon-o-shopping-cart')
                ->color('warning'),
            Stat::make('กู้คืนแล้ว (30 วัน)', $recovered30)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description("อัตราการกู้คืน {$recoveryRate}%"),
            Stat::make('มูลค่าที่ทิ้งรวม (30 วัน)', '฿'.number_format((float) $pendingValue))
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
        ];
    }
}
