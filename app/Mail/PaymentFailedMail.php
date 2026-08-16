<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment not completed — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_failed',
            with: [
                'payment' => $this->payment,
                'billing' => $this->payment->billing_data ?? [],
            ],
        );
    }
}
