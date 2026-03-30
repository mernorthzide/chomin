<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'รูปภาพ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_color_id')
                    ->label('สี')
                    ->relationship('color', 'name')
                    ->nullable(),
                FileUpload::make('image_path')
                    ->label('รูปภาพ')
                    ->image()
                    ->directory('products')
                    ->required(),
                Toggle::make('is_primary')
                    ->label('รูปหลัก')
                    ->default(false),
                TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image_path')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('รูปภาพ'),
                TextColumn::make('color.name')
                    ->label('สี'),
                IconColumn::make('is_primary')
                    ->label('รูปหลัก')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
