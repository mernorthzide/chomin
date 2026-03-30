<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSlip extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'image_path', 'uploaded_at', 'confirmed_at', 'confirmed_by', 'rejection_reason'];
    protected $casts = ['uploaded_at' => 'datetime', 'confirmed_at' => 'datetime'];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function confirmedByUser(): BelongsTo { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function isConfirmed(): bool { return $this->confirmed_at !== null; }
    public function isRejected(): bool { return $this->rejection_reason !== null && $this->confirmed_at === null; }
}
