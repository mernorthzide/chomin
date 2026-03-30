<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('รหัสคูปอง')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('ประเภท')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'fixed' ? 'บาท' : '%'),
                TextColumn::make('value')
                    ->label('มูลค่า')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_count')
                    ->label('ใช้แล้ว')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_uses')
                    ->label('ใช้ได้สูงสุด')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('หมดอายุ')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('สถานะ')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
