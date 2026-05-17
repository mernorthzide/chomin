<?php

namespace App\Filament\Resources\StoreLocations;

use App\Filament\Resources\StoreLocations\Pages\CreateStoreLocation;
use App\Filament\Resources\StoreLocations\Pages\EditStoreLocation;
use App\Filament\Resources\StoreLocations\Pages\ListStoreLocations;
use App\Models\StoreLocation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoreLocationResource extends Resource
{
    protected static ?string $model = StoreLocation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'เนื้อหา';

    protected static ?string $navigationLabel = 'Store locator';

    protected static ?string $modelLabel = 'Store location';

    protected static ?string $pluralModelLabel = 'Store locations';

    protected static ?int $navigationSort = 13;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ข้อมูลสาขา')
                ->schema([
                    TextInput::make('phone')->label('เบอร์โทร'),
                    TextInput::make('email')->label('อีเมล')->email(),
                    Textarea::make('map_url')->label('Google Maps URL')->columnSpanFull(),
                    TextInput::make('latitude')->label('Latitude')->numeric(),
                    TextInput::make('longitude')->label('Longitude')->numeric(),
                    Toggle::make('is_active')->label('เปิดใช้งาน')->default(true),
                    TextInput::make('sort_order')->label('ลำดับ')->numeric()->default(0),
                ])
                ->columns(2),
            Section::make('ชื่อ ที่อยู่ เวลาเปิดทำการ')
                ->schema([
                    Repeater::make('translations')
                        ->relationship()
                        ->schema([
                            Select::make('locale')->label('ภาษา')->options(config('chomin.locales.labels'))->required(),
                            TextInput::make('name')->label('ชื่อสาขา')->required(),
                            Textarea::make('address')->label('ที่อยู่')->columnSpanFull(),
                            Textarea::make('hours')->label('เวลาเปิดทำการ')->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')->label('โทร')->searchable(),
                TextColumn::make('email')->label('อีเมล')->searchable(),
                IconColumn::make('is_active')->label('เปิด')->boolean(),
                TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
                TextColumn::make('updated_at')->label('อัปเดต')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
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

    public static function getPages(): array
    {
        return [
            'index' => ListStoreLocations::route('/'),
            'create' => CreateStoreLocation::route('/create'),
            'edit' => EditStoreLocation::route('/{record}/edit'),
        ];
    }
}
