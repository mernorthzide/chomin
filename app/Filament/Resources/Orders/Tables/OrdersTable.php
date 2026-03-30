<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('เลขออเดอร์')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('ลูกค้า')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'รอชำระเงิน',
                        'awaiting_payment' => 'รอตรวจสอบ',
                        'paid' => 'ชำระเงินแล้ว',
                        'shipping' => 'กำลังจัดส่ง',
                        'completed' => 'สำเร็จ',
                        'cancelled' => 'ยกเลิก',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'awaiting_payment' => 'info',
                        'paid' => 'success',
                        'shipping' => 'primary',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('วันที่สั่ง')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'pending' => 'รอชำระเงิน',
                        'awaiting_payment' => 'รอตรวจสอบ',
                        'paid' => 'ชำระเงินแล้ว',
                        'shipping' => 'กำลังจัดส่ง',
                        'completed' => 'สำเร็จ',
                        'cancelled' => 'ยกเลิก',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('อนุมัติ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status === 'awaiting_payment')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'paid']);
                        if ($record->paymentSlip) {
                            $record->paymentSlip->update([
                                'confirmed_at' => now(),
                                'confirmed_by' => Auth::id(),
                            ]);
                        }
                    }),
                Action::make('reject')
                    ->label('ปฏิเสธ')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record): bool => $record->status === 'awaiting_payment')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('เหตุผลการปฏิเสธ')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['status' => 'pending']);
                        if ($record->paymentSlip) {
                            $record->paymentSlip->update([
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                        }
                    }),
                Action::make('ship')
                    ->label('จัดส่ง')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn ($record): bool => $record->status === 'paid')
                    ->form([
                        TextInput::make('tracking_number')
                            ->label('เลขพัสดุ')
                            ->required(),
                        TextInput::make('carrier_name')
                            ->label('ขนส่ง')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'shipping',
                            'tracking_number' => $data['tracking_number'],
                            'carrier_name' => $data['carrier_name'],
                            'shipped_at' => now(),
                        ]);
                    }),
                Action::make('complete')
                    ->label('เสร็จสิ้น')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->status === 'shipping')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
