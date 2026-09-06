<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactUs $contactMessage,
        public string $replyBody,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->contactMessage->subject
            ? 'Re: ' . $this->contactMessage->subject
            : 'Re: your message to ' . config('app.name');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.contact_reply',
            with: [
                'name'      => $this->contactMessage->name,
                'original'  => $this->contactMessage->message,
                'replyBody' => $this->replyBody,
            ],
        );
    }
}
