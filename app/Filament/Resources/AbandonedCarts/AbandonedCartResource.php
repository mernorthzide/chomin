<?php

namespace App\Filament\Resources\AbandonedCarts;

use App\Filament\Resources\AbandonedCarts\Pages\CreateAbandonedCart;
use App\Filament\Resources\AbandonedCarts\Pages\EditAbandonedCart;
use App\Filament\Resources\AbandonedCarts\Pages\ListAbandonedCarts;
use App\Filament\Resources\AbandonedCarts\Schemas\AbandonedCartForm;
use App\Filament\Resources\AbandonedCarts\Tables\AbandonedCartsTable;
use App\Models\AbandonedCart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AbandonedCartResource extends Resource
{
    protected static ?string $model = AbandonedCart::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'การขาย';

    protected static ?string $navigationLabel = 'ตะกร้าที่ถูกทิ้ง';

    protected static ?string $modelLabel = 'ตะกร้าที่ถูกทิ้ง';

    protected static ?string $pluralModelLabel = 'ตะกร้าที่ถูกทิ้ง';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return AbandonedCartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbandonedCartsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbandonedCarts::route('/'),
            'create' => CreateAbandonedCart::route('/create'),
            'edit' => EditAbandonedCart::route('/{record}/edit'),
        ];
    }
}
