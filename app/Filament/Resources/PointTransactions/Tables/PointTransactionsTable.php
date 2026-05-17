<?php

namespace App\Filament\Resources\PointTransactions\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PointTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'order']))
            ->columns([
                TextColumn::make('user.name')
                    ->label('สมาชิก')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('points')
                    ->label('แต้ม')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '').$state)
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('ประเภท')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'earned' => 'ได้รับ',
                        'redeemed' => 'ใช้แต้ม',
                        'expired' => 'หมดอายุ',
                        'adjusted' => 'ปรับแต้ม',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'earned' => 'success',
                        'redeemed' => 'warning',
                        'expired' => 'gray',
                        'adjusted' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('รายละเอียด')
                    ->placeholder('-')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('order.order_number')
                    ->label('เลขออเดอร์')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('ประเภท')
                    ->options([
                        'earned' => 'ได้รับ',
                        'redeemed' => 'ใช้แต้ม',
                        'expired' => 'หมดอายุ',
                        'adjusted' => 'ปรับแต้ม',
                    ]),
                Filter::make('created_at')
                    ->label('ช่วงวันที่')
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
