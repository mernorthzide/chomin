<?php

namespace App\Filament\Resources\GiftCardTransactions\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftCardTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['giftCard', 'order']))
            ->columns([
                TextColumn::make('giftCard.code_last4')
                    ->label('บัตรของขวัญ (4 หลักท้าย)')
                    ->formatStateUsing(fn ($state) => $state ? '****-'.$state : '-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('giftCard.recipient_name')
                    ->label('ผู้รับบัตร')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('ประเภท')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'issued' => 'ออกบัตร',
                        'redeemed' => 'ใช้บัตร',
                        'refunded' => 'คืนเงิน',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'success',
                        'redeemed' => 'warning',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('จำนวน (฿)')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('balance_after')
                    ->label('ยอดคงเหลือ (฿)')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('order.order_number')
                    ->label('เลขออเดอร์')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('note')
                    ->label('หมายเหตุ')
                    ->placeholder('-')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('ประเภท')
                    ->options([
                        'issued' => 'ออกบัตร',
                        'redeemed' => 'ใช้บัตร',
                        'refunded' => 'คืนเงิน',
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
