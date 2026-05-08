<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreLocationTranslation extends Model
{
    protected $fillable = ['store_location_id', 'locale', 'name', 'address', 'hours'];

    public function storeLocation(): BelongsTo
    {
        return $this->belongsTo(StoreLocation::class);
    }
}
