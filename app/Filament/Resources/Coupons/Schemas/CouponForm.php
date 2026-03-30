<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('รหัสคูปอง')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('type')
                    ->label('ประเภท')
                    ->options([
                        'fixed' => 'ลดตามจำนวน (บาท)',
                        'percent' => 'ลดตามเปอร์เซ็นต์ (%)',
                    ])
                    ->required()
                    ->live(),
                TextInput::make('value')
                    ->label('มูลค่า')
                    ->required()
                    ->numeric()
                    ->suffix(fn (\Filament\Forms\Get $get) => $get('type') === 'percent' ? '%' : 'บาท'),
                TextInput::make('max_discount')
                    ->label('ส่วนลดสูงสุด (บาท)')
                    ->numeric()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'percent'),
                TextInput::make('min_order_amount')
                    ->label('ยอดขั้นต่ำ')
                    ->numeric()
                    ->default(0),
                TextInput::make('max_uses')
                    ->label('ใช้ได้สูงสุด (ครั้ง)')
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->label('เริ่มใช้งาน'),
                DateTimePicker::make('expires_at')
                    ->label('หมดอายุ'),
                Toggle::make('is_active')
                    ->label('เปิดใช้งาน')
                    ->default(true),
            ]);
    }
}
