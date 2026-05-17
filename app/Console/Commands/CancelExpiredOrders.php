<?php

namespace App\Console\Commands;

use App\Mail\OrderCancelled;
use App\Models\Order;
use App\Services\GiftCardService;
use App\Support\SafeMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Cancel orders pending payment for more than 48 hours';

    public function handle(): void
    {
        $orders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->pluck('id');

        $processed = 0;
        $failed = 0;

        foreach ($orders as $orderId) {
            try {
                DB::transaction(function () use ($orderId) {
                    $order = Order::with(['items.variant', 'user', 'giftCardRedemptions.giftCard'])
                        ->lockForUpdate()
                        ->find($orderId);

                    if (! $order || $order->status !== 'pending') {
                        return;
                    }

                    foreach ($order->items as $item) {
                        $item->variant->increment('stock', $item->quantity);
                    }

                    if ($order->points_used > 0 && $order->user) {
                        $order->user->increment('points', $order->points_used);
                        $order->user->pointTransactions()->create([
                            'order_id' => $order->id,
                            'points' => $order->points_used,
                            'type' => 'adjust',
                            'description' => "คืนแต้ม — ยกเลิกออเดอร์ {$order->order_number}",
                        ]);
                    }

                    if ($order->gift_card_discount > 0) {
                        app(GiftCardService::class)->refundOrder($order);
                    }

                    $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                    if ($order->user?->email) {
                        SafeMail::queue($order->user->email, new OrderCancelled($order));
                    }

                    $this->info("Cancelled order {$order->order_number}");
                });

                $processed++;
            } catch (Throwable $e) {
                $failed++;
                Log::error('CancelExpiredOrders failed', [
                    'order_id' => $orderId,
                    'message' => $e->getMessage(),
                ]);
                $this->error("Failed to cancel order id {$orderId}: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$processed} expired orders ({$failed} failed).");
    }
}
