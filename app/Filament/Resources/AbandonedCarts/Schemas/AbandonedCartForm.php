<?php

namespace App\Filament\Resources\AbandonedCarts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AbandonedCartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('อีเมล')
                    ->disabled(),
                TextInput::make('total')
                    ->label('ยอดรวม')
                    ->disabled()
                    ->prefix('฿'),
                TextInput::make('reminder_count')
                    ->label('จำนวนครั้งที่เตือน')
                    ->numeric()
                    ->minValue(0),
                DateTimePicker::make('last_reminder_at')
                    ->label('เตือนล่าสุด'),
                DateTimePicker::make('recovered_at')
                    ->label('วันที่กู้คืน'),
            ]);
    }
}
