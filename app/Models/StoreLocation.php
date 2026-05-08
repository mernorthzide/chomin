<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreLocation extends Model
{
    use HasLocalizedContent;

    protected $fillable = ['phone', 'email', 'map_url', 'latitude', 'longitude', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];

    public function translations(): HasMany
    {
        return $this->hasMany(StoreLocationTranslation::class);
    }
}
