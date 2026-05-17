<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopProducts extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'สินค้าขายดี 30 วันล่าสุด';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'products.id', '=', 'order_items.product_id')
                    ->where('orders.created_at', '>=', now()->subDays(30))
                    ->select(
                        'products.id as id',
                        'products.name as product_name',
                        DB::raw('SUM(order_items.quantity) as sold'),
                        DB::raw('SUM(order_items.price * order_items.quantity) as revenue'),
                    )
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('sold')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')->label('สินค้า')->wrap(),
                Tables\Columns\TextColumn::make('sold')
                    ->label('ขายได้ (ชิ้น)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('revenue')
                    ->label('รายได้')
                    ->money('THB')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
