<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryTranslation extends Model
{
    protected $fillable = ['story_id', 'locale', 'title', 'excerpt', 'body', 'seo_title', 'seo_description'];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
