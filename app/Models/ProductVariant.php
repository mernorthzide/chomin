<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id', 'product_color_id', 'size', 'stock', 'sku'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function color(): BelongsTo { return $this->belongsTo(ProductColor::class, 'product_color_id'); }
    public function isInStock(): bool { return $this->stock > 0; }
}
