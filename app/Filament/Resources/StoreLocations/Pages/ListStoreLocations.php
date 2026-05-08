<?php

namespace App\Filament\Resources\StoreLocations\Pages;

use App\Filament\Resources\StoreLocations\StoreLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoreLocations extends ListRecords
{
    protected static string $resource = StoreLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
