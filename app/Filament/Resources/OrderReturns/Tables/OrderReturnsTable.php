<?php

namespace App\Filament\Resources\OrderReturns\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['order', 'user']))
            ->columns([
                TextColumn::make('rma_number')
                    ->label('RMA')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.order_number')
                    ->label('คำสั่งซื้อ')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('ลูกค้า')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('ประเภท')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'return' ? 'คืนเงิน' : 'เปลี่ยน'),
                TextColumn::make('reason')
                    ->label('เหตุผล')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'size_too_small' => 'ไซส์เล็กไป',
                        'size_too_large' => 'ไซส์ใหญ่ไป',
                        'color_different' => 'สีไม่ตรงภาพ',
                        'defective' => 'ชำรุด',
                        'not_as_described' => 'ไม่ตรงรายละเอียด',
                        'changed_mind' => 'เปลี่ยนใจ',
                        default => 'อื่น ๆ',
                    }),
                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'requested' => 'warning',
                        'approved', 'in_transit' => 'info',
                        'received', 'refunded' => 'success',
                        'rejected', 'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'requested' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติ',
                        'in_transit' => 'จัดส่งคืน',
                        'received' => 'รับคืนแล้ว',
                        'refunded' => 'คืนเงินแล้ว',
                        'rejected' => 'ไม่อนุมัติ',
                        'cancelled' => 'ยกเลิก',
                        default => $state,
                    }),
                TextColumn::make('refund_amount')
                    ->label('คืนเงิน')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('ส่งคำขอ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'requested' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติ',
                        'in_transit' => 'จัดส่งคืน',
                        'received' => 'รับคืนแล้ว',
                        'refunded' => 'คืนเงินแล้ว',
                        'rejected' => 'ไม่อนุมัติ',
                        'cancelled' => 'ยกเลิก',
                    ]),
                SelectFilter::make('type')
                    ->options(['return' => 'คืนเงิน', 'exchange' => 'เปลี่ยน']),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('markRefunded')
                    ->label('ออกเงินคืน')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record && in_array($record->status, ['approved', 'received']))
                    ->form([
                        TextInput::make('refund_amount')
                            ->label('จำนวนเงินคืน (฿)')
                            ->numeric()
                            ->required()
                            ->default(fn ($record) => $record?->refund_amount ?? 0),
                        TextInput::make('admin_note')
                            ->label('หมายเหตุ (เช่น เลขอ้างอิงการโอน)'),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => 'refunded',
                            'refund_amount' => (float) $data['refund_amount'],
                            'refunded_at' => now(),
                            'admin_note' => trim(($record->admin_note ? $record->admin_note."\n" : '').($data['admin_note'] ?? '')),
                        ]);

                        Notification::make()
                            ->title('บันทึกการคืนเงินเรียบร้อย')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
