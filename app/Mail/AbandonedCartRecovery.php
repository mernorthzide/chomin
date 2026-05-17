<?php

namespace App\Mail;

use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartRecovery extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public AbandonedCart $abandonedCart) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ลืมอะไรไว้ในตะกร้าไหม? 🛒',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart-recovery',
            with: ['abandonedCart' => $this->abandonedCart],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
