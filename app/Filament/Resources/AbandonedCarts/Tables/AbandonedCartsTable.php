<?php

namespace App\Filament\Resources\AbandonedCarts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbandonedCartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->columns([
                TextColumn::make('email')
                    ->label('อีเมล')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->default('Guest'),
                TextColumn::make('total')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('จำนวนรายการ')
                    ->getStateUsing(fn ($record) => count($record->items_snapshot ?? [])),
                TextColumn::make('reminder_count')
                    ->label('ส่งเตือนแล้ว')
                    ->sortable(),
                TextColumn::make('last_reminder_at')
                    ->label('เตือนล่าสุด')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('recovered_at')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'กู้คืนแล้ว' : 'ยังไม่ชำระ')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('recovered')
                    ->label('กู้คืนแล้ว')
                    ->query(fn (Builder $query) => $query->whereNotNull('recovered_at')),
                Filter::make('pending')
                    ->label('ยังไม่ชำระ')
                    ->query(fn (Builder $query) => $query->whereNull('recovered_at')),
                Filter::make('has_email')
                    ->label('มีอีเมล')
                    ->query(fn (Builder $query) => $query->whereNotNull('email')->where('email', '!=', '')),
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
