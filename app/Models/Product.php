<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'price', 'collection_id', 'category_id', 'is_active', 'is_featured', 'sort_order'];
    protected $casts = ['price' => 'decimal:2', 'is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function collection(): BelongsTo { return $this->belongsTo(Collection::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function colors(): HasMany { return $this->hasMany(ProductColor::class)->orderBy('sort_order'); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary', true); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function getTotalStockAttribute(): int { return $this->variants->sum('stock'); }
}
