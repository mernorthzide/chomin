<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'image', 'banner_image', 'is_active', 'sort_order', 'layout_type'];

    public const LAYOUT_TYPES = [
        'side-hero'       => 'รูปด้านข้าง + แผงข้อความ',
        'dark-editorial'  => 'พื้นดำ + หัวข้อ Italic ใหญ่',
        'centered-banner' => 'หัวข้อกลาง + แบนเนอร์กว้าง',
        'header-banner'   => 'หัวข้อ + ดูทั้งหมด + แบนเนอร์',
    ];
    protected $casts = ['is_active' => 'boolean'];
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('sort_order'); }
}
