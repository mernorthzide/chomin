<?php

namespace App\Filament\Resources\AbandonedCarts\Pages;

use App\Filament\Resources\AbandonedCarts\AbandonedCartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbandonedCart extends CreateRecord
{
    protected static string $resource = AbandonedCartResource::class;
}
