<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactUs $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your message — ' . config('app.name', 'SelfBuy'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.contact_acknowledgement',
            with: ['contact' => $this->contact],
        );
    }
}
