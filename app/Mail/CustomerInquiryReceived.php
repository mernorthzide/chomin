<?php

namespace App\Mail;

use App\Models\CustomerInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerInquiryReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CustomerInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "CHOMIN inquiry: {$this->inquiry->type}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer-inquiry-received',
            with: ['inquiry' => $this->inquiry],
        );
    }
}
