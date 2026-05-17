<?php

namespace App\Mail;

use App\Models\BackInStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackInStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public BackInStockNotification $notification) {}

    public function envelope(): Envelope
    {
        $productName = $this->notification->product->name ?? 'สินค้า';

        return new Envelope(
            subject: "{$productName} กลับมาแล้ว!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.back-in-stock-alert',
            with: ['notification' => $this->notification],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
