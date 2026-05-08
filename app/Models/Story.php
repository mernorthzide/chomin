<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    use HasLocalizedContent;

    protected $fillable = ['slug', 'cover_image', 'is_published', 'published_at', 'sort_order'];

    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];

    public function translations(): HasMany
    {
        return $this->hasMany(StoryTranslation::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
