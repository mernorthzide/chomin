<?php

namespace App\Filament\Resources\OrderReturns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rma_number')
                    ->label('RMA')
                    ->disabled(),
                TextInput::make('order.order_number')
                    ->label('คำสั่งซื้อ')
                    ->disabled(),
                Select::make('type')
                    ->label('ประเภท')
                    ->options(['return' => 'คืนเงิน', 'exchange' => 'เปลี่ยนสินค้า'])
                    ->disabled(),
                Select::make('reason')
                    ->label('เหตุผล')
                    ->options([
                        'size_too_small' => 'ไซส์เล็กไป',
                        'size_too_large' => 'ไซส์ใหญ่ไป',
                        'color_different' => 'สีไม่ตรงตามภาพ',
                        'defective' => 'สินค้าชำรุด',
                        'not_as_described' => 'ไม่ตรงกับรายละเอียด',
                        'changed_mind' => 'เปลี่ยนใจ',
                        'other' => 'อื่น ๆ',
                    ])
                    ->disabled(),
                Textarea::make('reason_detail')
                    ->label('รายละเอียดจากลูกค้า')
                    ->disabled()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('สถานะ')
                    ->options([
                        'requested' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติ',
                        'in_transit' => 'กำลังจัดส่งคืน',
                        'received' => 'รับสินค้าคืนแล้ว',
                        'refunded' => 'คืนเงินสำเร็จ',
                        'rejected' => 'ไม่อนุมัติ',
                        'cancelled' => 'ยกเลิก',
                    ])
                    ->required(),
                TextInput::make('refund_amount')
                    ->label('จำนวนเงินคืน')
                    ->numeric()
                    ->prefix('฿'),
                DateTimePicker::make('approved_at')->label('วันที่อนุมัติ'),
                DateTimePicker::make('refunded_at')->label('วันที่คืนเงิน'),
                Textarea::make('admin_note')
                    ->label('บันทึก / ตอบกลับลูกค้า')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
