<?php

namespace App\Console\Commands;

use App\Mail\ReviewRequest;
use App\Models\Order;
use App\Support\SafeMail;
use Illuminate\Console\Command;

class SendReviewRequests extends Command
{
    protected $signature = 'reviews:request {--dry-run : Log intended sends without dispatching}';

    protected $description = 'Send review request emails to customers whose orders completed 7-8 days ago';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');
        $dispatched = 0;

        $orders = Order::where('status', 'completed')
            ->whereBetween('completed_at', [now()->subDays(8), now()->subDays(7)])
            ->whereNull('review_request_sent_at')
            ->with(['user', 'items'])
            ->get();

        foreach ($orders as $order) {
            $email = $order->user?->email;

            if (blank($email)) {
                $this->warn("Skipping order {$order->order_number} — no user email found.");

                continue;
            }

            if ($dryRun) {
                $this->info("[dry-run] Would send review request to {$email} for order {$order->order_number}");
            } else {
                SafeMail::queue($email, new ReviewRequest($order));
                $order->update(['review_request_sent_at' => now()]);
                $this->info("Sent review request to {$email} for order {$order->order_number}");
            }
            $dispatched++;
        }

        $label = $dryRun ? 'Would dispatch' : 'Dispatched';
        $this->info("{$label} {$dispatched} review request email(s).");
    }
}
