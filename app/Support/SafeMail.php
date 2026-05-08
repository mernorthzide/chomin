<?php

namespace App\Support;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SafeMail
{
    public static function queue(?string $recipient, Mailable $mailable): bool
    {
        if (blank($recipient)) {
            return false;
        }

        try {
            Mail::to($recipient)->queue($mailable);

            return true;
        } catch (Throwable $exception) {
            Log::warning('Mail delivery skipped', [
                'recipient' => $recipient,
                'mailable' => $mailable::class,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
