<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorsRelationManager extends RelationManager
{
    protected static string $relationship = 'colors';

    protected static ?string $title = 'สี';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ชื่อสี')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color_code')
                    ->label('รหัสสี'),
                TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ColorColumn::make('color_code')
                    ->label('สี'),
                TextColumn::make('name')
                    ->label('ชื่อสี'),
                TextColumn::make('sort_order')
                    ->label('ลำดับ')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->reorderable('sort_order')
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
