<?php

namespace App\Filament\Resources\GiftCardTransactions\Pages;

use App\Filament\Resources\GiftCardTransactions\GiftCardTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListGiftCardTransactions extends ListRecords
{
    protected static string $resource = GiftCardTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
