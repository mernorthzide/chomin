<?php
namespace App\Services;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\SiteSetting;

class PointsService
{
    public function earnPoints(Order $order): int
    {
        $pointsPerBaht = (int) SiteSetting::get('points_per_baht', 100);
        if ($pointsPerBaht <= 0) return 0;

        $pointsEarned = (int) floor($order->total / $pointsPerBaht);
        if ($pointsEarned <= 0) return 0;

        $order->user->increment('points', $pointsEarned);
        $order->update(['points_earned' => $pointsEarned]);

        PointTransaction::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'points' => $pointsEarned,
            'type' => 'earn',
            'description' => "สั่งซื้อ {$order->order_number}",
        ]);

        return $pointsEarned;
    }
}
