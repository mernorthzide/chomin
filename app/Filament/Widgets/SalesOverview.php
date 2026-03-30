<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $todaysSales = Order::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->sum('total');

        $monthlySales = Order::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('total');

        $awaitingPayment = Order::where('status', 'awaiting_payment')->count();

        $newMembers = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('ยอดขายวันนี้', '฿' . number_format($todaysSales, 2))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('ยอดขายเดือนนี้', '฿' . number_format($monthlySales, 2))
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
            Stat::make('รอตรวจสลิป', $awaitingPayment . ' ออเดอร์')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('warning'),
            Stat::make('สมาชิกใหม่เดือนนี้', $newMembers . ' คน')
                ->icon('heroicon-o-user-plus')
                ->color('info'),
        ];
    }
}
