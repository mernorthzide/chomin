<?php

namespace App\Mail;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $locale,
        public ?Coupon $coupon = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->locale === 'en'
            ? 'Welcome to CHOMIN'
            : 'ยินดีต้อนรับสู่ CHOMIN';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter-welcome',
            with: [
                'email' => $this->email,
                'locale' => $this->locale,
                'coupon' => $this->coupon,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
