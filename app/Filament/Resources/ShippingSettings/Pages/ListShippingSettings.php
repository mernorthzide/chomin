<?php

namespace App\Filament\Resources\ShippingSettings\Pages;

use App\Filament\Resources\ShippingSettings\ShippingSettingResource;
use App\Models\ShippingSetting;
use Filament\Resources\Pages\ListRecords;

class ListShippingSettings extends ListRecords
{
    protected static string $resource = ShippingSettingResource::class;

    public function mount(): void
    {
        $record = ShippingSetting::current();
        $this->redirect(ShippingSettingResource::getUrl('edit', ['record' => $record]));
    }
}
