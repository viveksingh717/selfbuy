<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactUs $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact message: ' . ($this->contact->subject ?: 'No subject'),
            replyTo: [new Address($this->contact->email, $this->contact->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.contact_admin_notification',
            with: [
                'contact'   => $this->contact,
                'adminUrl'  => url('/admin/contact_us'),
            ],
        );
    }
}
