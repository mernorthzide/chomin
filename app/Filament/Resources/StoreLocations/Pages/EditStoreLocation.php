<?php

namespace App\Filament\Resources\StoreLocations\Pages;

use App\Filament\Resources\StoreLocations\StoreLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStoreLocation extends EditRecord
{
    protected static string $resource = StoreLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
