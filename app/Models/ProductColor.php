<?php
namespace App\Models;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function getFilterKeyAttribute(): string
    {
        if (filled($this->slug)) {
            return $this->slug;
        }

        $swatch = $this->relationLoaded('images')
            ? $this->images->first(fn (ProductImage $image): bool => str_starts_with($image->image_path, 'products/colors/'))
            : $this->images()
                ->where('image_path', 'like', 'products/colors/%')
                ->orderBy('sort_order')
                ->first();

        if ($swatch) {
            return pathinfo($swatch->image_path, PATHINFO_FILENAME);
        }

        return Str::slug($this->name) ?: $this->name;
    }
}
