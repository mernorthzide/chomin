<?php

namespace App\Filament\Resources\ShippingSettings;

use App\Filament\Resources\ShippingSettings\Pages\EditShippingSetting;
use App\Filament\Resources\ShippingSettings\Pages\ListShippingSettings;
use App\Filament\Resources\ShippingSettings\Schemas\ShippingSettingForm;
use App\Filament\Resources\ShippingSettings\Tables\ShippingSettingsTable;
use App\Models\ShippingSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ShippingSettingResource extends Resource
{
    protected static ?string $model = ShippingSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'ตั้งค่า';

    protected static ?string $navigationLabel = 'ค่าจัดส่ง';

    protected static ?string $modelLabel = 'การตั้งค่าจัดส่ง';

    protected static ?string $pluralModelLabel = 'การตั้งค่าจัดส่ง';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ShippingSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingSettings::route('/'),
            'edit' => EditShippingSetting::route('/{record}/edit'),
        ];
    }
}
