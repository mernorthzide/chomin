<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'product_id', 'product_variant_id', 'product_name', 'color_name', 'size', 'custom_options', 'price', 'quantity'];
    protected $casts = ['price' => 'decimal:2', 'custom_options' => 'array'];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function variant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'product_variant_id'); }
    public function getLineTotalAttribute(): float { return $this->price * $this->quantity; }
    public function getVariantLabelAttribute(): string
    {
        return collect([$this->color_name, $this->size])->filter()->implode(' / ');
    }

    public function getCustomOptionLabelsAttribute(): array
    {
        $groups = config('chomin.custom_options', []);
        $options = $this->custom_options ?? [];

        return collect($groups)
            ->map(function (array $group, string $groupKey) use ($options): ?string {
                $value = $options[$groupKey] ?? null;
                $label = is_string($value) ? ($group['options'][$value] ?? null) : null;

                if (! $label) {
                    return null;
                }

                return "{$group['label']}: {$label}";
            })
            ->filter()
            ->values()
            ->all();
    }

    public function getCustomOptionsTextAttribute(): ?string
    {
        $labels = $this->custom_option_labels;

        return $labels === [] ? null : implode("\n", $labels);
    }
}
