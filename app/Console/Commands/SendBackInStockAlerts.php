<?php

namespace App\Console\Commands;

use App\Mail\BackInStockAlert;
use App\Models\BackInStockNotification;
use App\Support\SafeMail;
use Illuminate\Console\Command;

class SendBackInStockAlerts extends Command
{
    protected $signature = 'stock:send-alerts {--dry-run : Log intended sends without dispatching}';

    protected $description = 'Send back-in-stock alert emails for items that are now available';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');
        $dispatched = 0;

        $notifications = BackInStockNotification::whereNull('notified_at')
            ->with(['product', 'variant'])
            ->get();

        foreach ($notifications as $notification) {
            // Check stock: prefer variant-level, fall back to product-level
            $inStock = false;

            if ($notification->product_variant_id && $notification->variant) {
                $inStock = $notification->variant->isInStock();
            } elseif ($notification->product) {
                $inStock = $notification->product->total_stock > 0;
            }

            if (! $inStock) {
                continue;
            }

            $productName = $notification->product->name ?? 'สินค้า';

            if ($dryRun) {
                $this->info("[dry-run] Would send back-in-stock alert to {$notification->email} for {$productName}");
            } else {
                SafeMail::queue($notification->email, new BackInStockAlert($notification));
                $notification->update(['notified_at' => now()]);
                $this->info("Sent back-in-stock alert to {$notification->email} for {$productName}");
            }
            $dispatched++;
        }

        $label = $dryRun ? 'Would dispatch' : 'Dispatched';
        $this->info("{$label} {$dispatched} back-in-stock alert email(s).");
    }
}
