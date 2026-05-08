<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPageTranslation extends Model
{
    protected $fillable = [
        'content_page_id', 'locale', 'title', 'excerpt', 'body', 'seo_title', 'seo_description',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class, 'content_page_id');
    }
}
