<?php
namespace App\Console\Commands;

use App\Mail\OrderCancelled;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel orders pending payment for more than 48 hours';

    public function handle(): void
    {
        $orders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $item->variant->increment('stock', $item->quantity);
            }

            if ($order->points_used > 0) {
                $order->user->increment('points', $order->points_used);
                $order->user->pointTransactions()->create([
                    'order_id' => $order->id,
                    'points' => $order->points_used,
                    'type' => 'adjust',
                    'description' => "คืนแต้ม — ยกเลิกออเดอร์ {$order->order_number}",
                ]);
            }

            $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            Mail::to($order->user->email)->queue(new OrderCancelled($order));
            $this->info("Cancelled order {$order->order_number}");
        }

        $this->info("Processed {$orders->count()} expired orders.");
    }
}
