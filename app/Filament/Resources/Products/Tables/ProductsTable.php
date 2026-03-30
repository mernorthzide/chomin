<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryImage.image_path')
                    ->label('รูปภาพ'),
                TextColumn::make('name')
                    ->label('ชื่อสินค้า')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('price')
                    ->label('ราคา')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('collection.name')
                    ->label('คอลเล็คชัน'),
                TextColumn::make('category.name')
                    ->label('หมวดหมู่'),
                TextColumn::make('variants_sum_stock')
                    ->label('สต็อกรวม')
                    ->sum('variants', 'stock')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('สถานะ')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('แนะนำ')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('collection_id')
                    ->label('คอลเล็คชัน')
                    ->relationship('collection', 'name'),
                SelectFilter::make('category_id')
                    ->label('หมวดหมู่')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('เปิดใช้งาน'),
                TernaryFilter::make('is_featured')
                    ->label('สินค้าแนะนำ'),
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
