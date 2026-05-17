<?php

namespace App\Filament\Resources\BackInStockNotifications;

use App\Filament\Resources\BackInStockNotifications\Pages\CreateBackInStockNotification;
use App\Filament\Resources\BackInStockNotifications\Pages\EditBackInStockNotification;
use App\Filament\Resources\BackInStockNotifications\Pages\ListBackInStockNotifications;
use App\Filament\Resources\BackInStockNotifications\Schemas\BackInStockNotificationForm;
use App\Filament\Resources\BackInStockNotifications\Tables\BackInStockNotificationsTable;
use App\Models\BackInStockNotification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BackInStockNotificationResource extends Resource
{
    protected static ?string $model = BackInStockNotification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'สินค้า';

    protected static ?string $navigationLabel = 'แจ้งเตือนสินค้ากลับมา';

    protected static ?string $modelLabel = 'การแจ้งเตือน';

    protected static ?string $pluralModelLabel = 'แจ้งเตือนสินค้ากลับมา';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return BackInStockNotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackInStockNotificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackInStockNotifications::route('/'),
            'create' => CreateBackInStockNotification::route('/create'),
            'edit' => EditBackInStockNotification::route('/{record}/edit'),
        ];
    }
}
