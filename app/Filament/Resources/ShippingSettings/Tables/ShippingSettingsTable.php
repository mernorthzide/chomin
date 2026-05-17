<?php

namespace App\Filament\Resources\ShippingSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shipping_fee')
                    ->label('ค่าจัดส่ง')
                    ->money('THB'),
                TextColumn::make('free_shipping_min_amount')
                    ->label('ยอดขั้นต่ำจัดส่งฟรี')
                    ->money('THB'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
