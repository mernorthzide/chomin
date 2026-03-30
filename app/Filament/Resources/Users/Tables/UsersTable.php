<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\PointTransaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('ชื่อ')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('อีเมล')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('เบอร์โทร'),
                TextColumn::make('points')
                    ->label('แต้ม')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('orders_count')
                    ->label('ออเดอร์')
                    ->counts('orders')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('สมัครเมื่อ')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('adjustPoints')
                    ->label('ปรับแต้ม')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->form([
                        TextInput::make('points')
                            ->label('จำนวนแต้ม (ใส่ + หรือ - ได้)')
                            ->numeric()
                            ->required(),
                        TextInput::make('description')
                            ->label('หมายเหตุ')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $points = (int) $data['points'];
                        $record->increment('points', $points);
                        PointTransaction::create([
                            'user_id' => $record->id,
                            'points' => $points,
                            'type' => $points >= 0 ? 'earn' : 'use',
                            'description' => $data['description'],
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
