<?php

namespace App\Filament\Resources\CustomerInquiries\Pages;

use App\Filament\Resources\CustomerInquiries\CustomerInquiryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerInquiry extends EditRecord
{
    protected static string $resource = CustomerInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
