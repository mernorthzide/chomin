<?php

namespace App\Filament\Resources\OrderReturns;

use App\Filament\Resources\OrderReturns\Pages\EditOrderReturn;
use App\Filament\Resources\OrderReturns\Pages\ListOrderReturns;
use App\Filament\Resources\OrderReturns\Schemas\OrderReturnForm;
use App\Filament\Resources\OrderReturns\Tables\OrderReturnsTable;
use App\Models\OrderReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderReturnResource extends Resource
{
    protected static ?string $model = OrderReturn::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static string|\UnitEnum|null $navigationGroup = 'การขาย';

    protected static ?string $navigationLabel = 'คืน/เปลี่ยนสินค้า';

    protected static ?string $modelLabel = 'คำขอคืนสินค้า';

    protected static ?string $pluralModelLabel = 'คำขอคืนสินค้า';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'requested')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return OrderReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderReturnsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderReturns::route('/'),
            'edit' => EditOrderReturn::route('/{record}/edit'),
        ];
    }
}
