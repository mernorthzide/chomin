<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id', 'product_color_id', 'image_path', 'is_primary', 'sort_order'];
    protected $casts = ['is_primary' => 'boolean'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function color(): BelongsTo { return $this->belongsTo(ProductColor::class, 'product_color_id'); }
}
