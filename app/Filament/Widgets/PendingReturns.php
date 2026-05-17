<?php

namespace App\Filament\Widgets;

use App\Models\OrderReturn;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingReturns extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'คำขอคืน/เปลี่ยนสินค้าที่รอตรวจสอบ';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderReturn::query()
                    ->whereIn('status', ['requested', 'approved', 'in_transit'])
                    ->with('order', 'user')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('rma_number')->label('RMA'),
                Tables\Columns\TextColumn::make('order.order_number')->label('คำสั่งซื้อ'),
                Tables\Columns\TextColumn::make('user.name')->label('ลูกค้า'),
                Tables\Columns\TextColumn::make('type')
                    ->label('ประเภท')
                    ->formatStateUsing(fn (string $state) => $state === 'return' ? 'คืนเงิน' : 'เปลี่ยน'),
                Tables\Columns\TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'requested' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'requested' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติ',
                        'in_transit' => 'จัดส่งคืน',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('refund_amount')->label('คืนเงิน')->money('THB'),
                Tables\Columns\TextColumn::make('created_at')->label('ยื่นเมื่อ')->dateTime('d/m/Y H:i'),
            ])
            ->paginated([5, 10]);
    }
}
