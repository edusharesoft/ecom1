<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryBoyPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $riderName
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'পাসওয়ার্ড রিসেট — ডেলিভারি পোর্টাল',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.delivery-boy-reset-text',
            with: [
                'resetUrl'  => $this->resetUrl,
                'riderName' => $this->riderName,
                'expire'    => (int) config('auth.passwords.delivery_boys.expire', 60),
            ],
        );
    }
}
