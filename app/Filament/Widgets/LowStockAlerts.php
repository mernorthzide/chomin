<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlerts extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'สินค้าใกล้หมดสต๊อก';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->where('stock', '<=', 5)
                    ->where('stock', '>', 0)
                    ->whereHas('product', fn ($q) => $q->where('is_active', true))
                    ->with('product')
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('สินค้า')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('ไซส์'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('คงเหลือ')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        default => 'warning',
                    }),
            ])
            ->paginated([5, 10, 20]);
    }
}
