<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDetailsChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{label: string, old: string, new: string}>  $changes
     */
    public function __construct(public string $name, public array $changes, public string $changedAt)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your '.config('app.name').' account details were updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.account_details_changed',
            with: ['name' => $this->name, 'changes' => $this->changes, 'changedAt' => $this->changedAt],
        );
    }
}
