<?php

namespace App\Filament\Resources\BackInStockNotifications\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BackInStockNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['product', 'variant', 'user']))
            ->columns([
                TextColumn::make('product.name')
                    ->label('สินค้า')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('variant_info')
                    ->label('ไซส์ / สี')
                    ->getStateUsing(function ($record) {
                        if ($record->variant) {
                            return collect([$record->variant->size, $record->variant->color])
                                ->filter()
                                ->implode(' / ');
                        }

                        return collect([$record->size, $record->color])
                            ->filter()
                            ->implode(' / ');
                    }),
                TextColumn::make('email')
                    ->label('อีเมล')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->default('Guest'),
                TextColumn::make('notified_at')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'แจ้งแล้ว' : 'รอแจ้ง')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('notified')
                    ->label('แจ้งแล้ว')
                    ->query(fn (Builder $query) => $query->whereNotNull('notified_at')),
                Filter::make('pending')
                    ->label('รอแจ้ง')
                    ->query(fn (Builder $query) => $query->whereNull('notified_at')),
                SelectFilter::make('product_id')
                    ->label('สินค้า')
                    ->options(fn () => Product::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
