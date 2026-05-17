<?php

namespace App\Filament\Resources\GiftCardTransactions;

use App\Filament\Resources\GiftCardTransactions\Pages\ListGiftCardTransactions;
use App\Filament\Resources\GiftCardTransactions\Tables\GiftCardTransactionsTable;
use App\Models\GiftCardTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransactionResource extends Resource
{
    protected static ?string $model = GiftCardTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'การเงิน';

    protected static ?string $navigationLabel = 'รายการบัตรของขวัญ';

    protected static ?string $modelLabel = 'รายการบัตรของขวัญ';

    protected static ?string $pluralModelLabel = 'รายการบัตรของขวัญ';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return GiftCardTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGiftCardTransactions::route('/'),
        ];
    }
}
