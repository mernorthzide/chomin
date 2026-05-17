<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ข้อมูลออเดอร์')
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('เลขออเดอร์'),
                        TextEntry::make('user.name')
                            ->label('ลูกค้า'),
                        TextEntry::make('status')
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
                        TextEntry::make('created_at')
                            ->label('วันที่สั่ง')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('ที่อยู่จัดส่ง')
                    ->schema([
                        TextEntry::make('shipping_name')
                            ->label('ชื่อผู้รับ'),
                        TextEntry::make('shipping_phone')
                            ->label('เบอร์โทร'),
                        TextEntry::make('shipping_address')
                            ->label('ที่อยู่')
                            ->columnSpanFull(),
                        TextEntry::make('shipping_district')
                            ->label('อำเภอ/เขต'),
                        TextEntry::make('shipping_province')
                            ->label('จังหวัด'),
                        TextEntry::make('shipping_postal_code')
                            ->label('รหัสไปรษณีย์'),
                    ])
                    ->columns(2),
                Section::make('ยอดรวม')
                    ->schema([
                        TextEntry::make('payment_method_label')
                            ->label('วิธีชำระเงิน')
                            ->placeholder('-'),
                        TextEntry::make('subtotal')
                            ->label('ยอดสินค้า')
                            ->money('THB'),
                        TextEntry::make('shipping_fee')
                            ->label('ค่าส่ง')
                            ->money('THB'),
                        TextEntry::make('cod_fee')
                            ->label('ค่าธรรมเนียม COD')
                            ->money('THB')
                            ->visible(fn ($record): bool => $record !== null && (float) $record->cod_fee > 0),
                        TextEntry::make('discount')
                            ->label('ส่วนลด')
                            ->money('THB'),
                        TextEntry::make('gift_card_discount')
                            ->label('บัตรของขวัญ')
                            ->money('THB'),
                        TextEntry::make('total')
                            ->label('ยอดรวม')
                            ->money('THB'),
                        TextEntry::make('points_earned')
                            ->label('แต้มที่ได้รับ'),
                        TextEntry::make('points_used')
                            ->label('แต้มที่ใช้'),
                    ])
                    ->columns(3),
                Section::make('รายการสินค้า')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('สินค้า')
                                    ->weight('medium'),
                                TextEntry::make('variant_label')
                                    ->label('สี / ไซส์')
                                    ->placeholder('-'),
                                TextEntry::make('custom_option_labels')
                                    ->label('แบบเสื้อ')
                                    ->listWithLineBreaks()
                                    ->placeholder('-'),
                                TextEntry::make('quantity')
                                    ->label('จำนวน')
                                    ->numeric(),
                                TextEntry::make('price')
                                    ->label('ราคาต่อชิ้น')
                                    ->money('THB'),
                                TextEntry::make('line_total')
                                    ->label('รวม')
                                    ->money('THB'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
                Section::make('ข้อมูลจัดส่ง')
                    ->schema([
                        TextEntry::make('tracking_number')
                            ->label('เลขพัสดุ')
                            ->placeholder('-'),
                        TextEntry::make('carrier_name')
                            ->label('ขนส่ง')
                            ->placeholder('-'),
                        TextEntry::make('shipped_at')
                            ->label('วันที่จัดส่ง')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('completed_at')
                            ->label('วันที่สำเร็จ')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->visible(fn ($record): bool => $record !== null && in_array($record->status, ['shipping', 'completed'])),
                Section::make('สลิปการโอน')
                    ->schema([
                        ImageEntry::make('paymentSlip.image_path')
                            ->label('สลิป'),
                        TextEntry::make('paymentSlip.rejection_reason')
                            ->label('เหตุผลการปฏิเสธ')
                            ->placeholder('-'),
                    ])
                    ->visible(fn ($record): bool => $record?->paymentSlip !== null),
                Section::make('หมายเหตุ')
                    ->schema([
                        TextEntry::make('note')
                            ->label('หมายเหตุ')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => ! empty($record?->note)),
            ]);
    }
}
