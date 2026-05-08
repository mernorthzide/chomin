<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContentPage extends Model
{
    use HasLocalizedContent;

    protected $fillable = ['slug', 'template', 'is_published', 'sort_order'];

    protected $casts = ['is_published' => 'boolean'];

    public function translations(): HasMany
    {
        return $this->hasMany(ContentPageTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ContentPageTranslation::class)->where('locale', app()->getLocale());
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
