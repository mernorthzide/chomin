<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('order_number')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'awaiting_payment' => 'Awaiting payment',
            'paid' => 'Paid',
            'shipping' => 'Shipping',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_fee')
                    ->required()
                    ->numeric(),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('points_used')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('coupon_id')
                    ->relationship('coupon', 'id'),
                TextInput::make('shipping_name')
                    ->required(),
                TextInput::make('shipping_phone')
                    ->tel()
                    ->required(),
                Textarea::make('shipping_address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('shipping_district')
                    ->required(),
                TextInput::make('shipping_province')
                    ->required(),
                TextInput::make('shipping_postal_code')
                    ->required(),
                TextInput::make('tracking_number'),
                TextInput::make('carrier_name'),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('completed_at'),
                DateTimePicker::make('cancelled_at'),
                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
