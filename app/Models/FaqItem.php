<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqItem extends Model
{
    use HasLocalizedContent;

    protected $fillable = ['category', 'is_published', 'sort_order'];

    protected $casts = ['is_published' => 'boolean'];

    public function translations(): HasMany
    {
        return $this->hasMany(FaqItemTranslation::class);
    }
}
