<?php

namespace App\Mail;

use App\Models\CouponModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent once, when a new customer account is verified - carries their welcome coupon. */
class WelcomeOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public CouponModel $coupon)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name', 'SelfBuy') . ' - here is your welcome offer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.welcome_offer',
            with: [
                'user'    => $this->user,
                'coupon'  => $this->coupon,
                'shopUrl' => route('products'),
            ],
        );
    }
}
