<?php

namespace App\Filament\Resources\CustomerInquiries\Pages;

use App\Filament\Resources\CustomerInquiries\CustomerInquiryResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomerInquiries extends ListRecords
{
    protected static string $resource = CustomerInquiryResource::class;
}
