<?php

namespace App\Filament\Resources\ShippingSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShippingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shipping_fee')
                    ->label('ค่าจัดส่งมาตรฐาน')
                    ->numeric()
                    ->prefix('฿')
                    ->required()
                    ->minValue(0),
                TextInput::make('free_shipping_min_amount')
                    ->label('ยอดซื้อขั้นต่ำสำหรับจัดส่งฟรี')
                    ->numeric()
                    ->prefix('฿')
                    ->helperText('เว้นว่างหากไม่มีโปรโมชั่นจัดส่งฟรี')
                    ->minValue(0),
            ]);
    }
}
