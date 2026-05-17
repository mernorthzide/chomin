<?php

namespace App\Filament\Resources\ProductReviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('สินค้า')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('name')
                    ->label('ผู้รีวิว')
                    ->description(fn ($record) => $record->user?->name)
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('คะแนน')
                    ->badge()
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color(fn (int $state) => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('title')
                    ->label('หัวข้อ')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'approved' => 'อนุมัติแล้ว',
                        'pending' => 'รอตรวจสอบ',
                        'rejected' => 'ไม่อนุมัติ',
                        default => $state,
                    }),
                IconColumn::make('is_verified_purchase')
                    ->label('ซื้อจริง')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'pending' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ไม่อนุมัติ',
                    ]),
                SelectFilter::make('rating')
                    ->label('คะแนน')
                    ->options([
                        '5' => '★★★★★ (5)',
                        '4' => '★★★★☆ (4)',
                        '3' => '★★★☆☆ (3)',
                        '2' => '★★☆☆☆ (2)',
                        '1' => '★☆☆☆☆ (1)',
                    ]),
                Filter::make('is_verified_purchase')
                    ->label('ซื้อจริงเท่านั้น')
                    ->query(fn (Builder $query) => $query->where('is_verified_purchase', true)),
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
