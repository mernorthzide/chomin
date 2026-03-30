<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'ตัวเลือก (Size + Stock)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_color_id')
                    ->label('สี')
                    ->relationship('color', 'name')
                    ->nullable(),
                Select::make('size')
                    ->label('ไซส์')
                    ->options([
                        'XS' => 'XS',
                        'S' => 'S',
                        'M' => 'M',
                        'L' => 'L',
                        'XL' => 'XL',
                        'XXL' => 'XXL',
                        'Free Size' => 'Free Size',
                    ])
                    ->required(),
                TextInput::make('stock')
                    ->label('จำนวนสต็อก')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->unique(ignoreRecord: true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('size')
            ->columns([
                TextColumn::make('color.name')
                    ->label('สี'),
                TextColumn::make('size')
                    ->label('ไซส์'),
                TextColumn::make('stock')
                    ->label('สต็อก')
                    ->numeric(),
                TextColumn::make('sku')
                    ->label('SKU'),
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
