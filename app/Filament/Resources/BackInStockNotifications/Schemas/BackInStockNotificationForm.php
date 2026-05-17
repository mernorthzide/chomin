<?php

namespace App\Filament\Resources\BackInStockNotifications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BackInStockNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product.name')
                    ->label('สินค้า')
                    ->disabled(),
                TextInput::make('email')
                    ->label('อีเมล')
                    ->email()
                    ->required(),
                TextInput::make('size')
                    ->label('ไซส์'),
                TextInput::make('color')
                    ->label('สี'),
                DateTimePicker::make('notified_at')
                    ->label('วันที่แจ้งเตือน'),
            ]);
    }
}
