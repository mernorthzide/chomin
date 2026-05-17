<?php

namespace App\Filament\Resources\Wishlists\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WishlistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'product']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('สมาชิก')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('สินค้า')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('วันที่เพิ่ม')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('ช่วงวันที่เพิ่ม')
                    ->form([
                        DatePicker::make('from')->label('ตั้งแต่'),
                        DatePicker::make('until')->label('ถึง'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
