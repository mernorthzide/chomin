<?php
namespace App\Models;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductColor extends Model
{
    use HasLocalizedContent;

    public $timestamps = false;
    protected $fillable = ['product_id', 'name', 'slug', 'color_code', 'sort_order'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function translations(): HasMany { return $this->hasMany(ProductColorTranslation::class); }
    public function getLocalizedNameAttribute(): string { return $this->localized('name'); }
}
