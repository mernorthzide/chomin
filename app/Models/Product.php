<?php
namespace App\Models;
use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasLocalizedContent;
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'sale_price', 'sale_starts_at', 'sale_ends_at',
        'collection_id', 'category_id', 'is_active', 'is_featured', 'sort_order',
    ];
    protected $casts = [
        'price' => 'decimal:2', 'sale_price' => 'decimal:2',
        'sale_starts_at' => 'datetime', 'sale_ends_at' => 'datetime',
        'is_active' => 'boolean', 'is_featured' => 'boolean',
    ];

    public function collection(): BelongsTo { return $this->belongsTo(Collection::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function colors(): HasMany { return $this->hasMany(ProductColor::class)->orderBy('sort_order'); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function translations(): HasMany { return $this->hasMany(ProductTranslation::class); }
    public function reviews(): HasMany { return $this->hasMany(ProductReview::class); }
    public function approvedReviews(): HasMany { return $this->hasMany(ProductReview::class)->where('status', 'approved')->latest('approved_at'); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('is_primary', true); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->where(fn ($q) => $q->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', now()));
    }
    public function getTotalStockAttribute(): int { return $this->variants->sum('stock'); }
    public function getLocalizedNameAttribute(): string { return $this->localized('name'); }
    public function getLocalizedDescriptionAttribute(): ?string { return $this->localized('description'); }
    public function getDisplayPriceAttribute(): float { return $this->is_on_sale ? (float) $this->sale_price : (float) $this->price; }
    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null
            && (float) $this->sale_price < (float) $this->price
            && (!$this->sale_starts_at || $this->sale_starts_at->lte(now()))
            && (!$this->sale_ends_at || $this->sale_ends_at->gte(now()));
    }

    public function getAverageRatingAttribute(): ?float
    {
        if (! $this->relationLoaded('approvedReviews')) {
            $avg = $this->approvedReviews()->avg('rating');
            return $avg ? round((float) $avg, 1) : null;
        }
        return $this->approvedReviews->count() > 0
            ? round($this->approvedReviews->avg('rating'), 1)
            : null;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews->count()
            : $this->approvedReviews()->count();
    }
}
