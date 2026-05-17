<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartRecovery;
use App\Models\AbandonedCart;
use App\Support\SafeMail;
use Illuminate\Console\Command;

class SendAbandonedCartEmails extends Command
{
    protected $signature = 'carts:send-recovery {--dry-run : Log intended sends without dispatching}';

    protected $description = 'Send abandoned cart recovery emails (up to 2 reminders)';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');
        $dispatched = 0;

        // First reminder: reminder_count=0, updated more than 4 hours ago
        $firstReminders = AbandonedCart::pending()
            ->where('reminder_count', 0)
            ->where('updated_at', '<', now()->subHours(4))
            ->whereNotNull('email')
            ->get();

        foreach ($firstReminders as $cart) {
            if ($dryRun) {
                $this->info("[dry-run] Would send first reminder to {$cart->email} (cart #{$cart->id}, total ฿{$cart->total})");
            } else {
                SafeMail::queue($cart->email, new AbandonedCartRecovery($cart));
                $cart->update([
                    'reminder_count' => 1,
                    'last_reminder_at' => now(),
                ]);
                $this->info("Sent first reminder to {$cart->email} (cart #{$cart->id})");
            }
            $dispatched++;
        }

        // Second reminder: reminder_count=1, last_reminder_at more than 24 hours ago
        $secondReminders = AbandonedCart::pending()
            ->where('reminder_count', 1)
            ->where('last_reminder_at', '<', now()->subHours(24))
            ->whereNotNull('email')
            ->get();

        foreach ($secondReminders as $cart) {
            if ($dryRun) {
                $this->info("[dry-run] Would send second reminder to {$cart->email} (cart #{$cart->id}, total ฿{$cart->total})");
            } else {
                SafeMail::queue($cart->email, new AbandonedCartRecovery($cart));
                $cart->update([
                    'reminder_count' => 2,
                    'last_reminder_at' => now(),
                ]);
                $this->info("Sent second reminder to {$cart->email} (cart #{$cart->id})");
            }
            $dispatched++;
        }

        $label = $dryRun ? 'Would dispatch' : 'Dispatched';
        $this->info("{$label} {$dispatched} abandoned cart recovery email(s).");
    }
}
