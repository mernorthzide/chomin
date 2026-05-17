<?php

namespace App\Filament\Widgets;

use App\Models\BackInStockNotification;
use App\Models\ProductReview;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingReviewsCount extends StatsOverviewWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        $pendingReviews = ProductReview::where('status', 'pending')->count();

        $approvedThisMonth = ProductReview::approved()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $backInStockPending = BackInStockNotification::whereNull('notified_at')->count();

        return [
            Stat::make('รีวิวรอตรวจสอบ', $pendingReviews)
                ->icon('heroicon-o-star')
                ->color('warning')
                ->url('/admin/product-reviews?tableFilters[status][value]=pending'),
            Stat::make('รีวิวอนุมัติแล้ว (เดือนนี้)', $approvedThisMonth)
                ->icon('heroicon-o-hand-thumb-up')
                ->color('success'),
            Stat::make('แจ้งเตือนสต๊อกกลับมารอส่ง', $backInStockPending)
                ->icon('heroicon-o-bell-alert')
                ->color('info'),
        ];
    }
}
