<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use App\Services\OfferCouponService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to the ' . config('app.name', 'SelfBuy') . ' newsletter',
        );
    }

    /** Lets mail clients show their own "Unsubscribe" button. */
    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<' . $this->unsubscribeUrl() . '>',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.newsletter_welcome',
            with: [
                'subscriber'     => $this->subscriber,
                // Only show a code they can still use (a returning subscriber's may be used/expired).
                'coupon'         => OfferCouponService::isUsable($this->subscriber->coupon) ? $this->subscriber->coupon : null,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
                'shopUrl'        => route('products'),
            ],
        );
    }

    /** Signed, so nobody can unsubscribe someone else by guessing the id. */
    private function unsubscribeUrl(): string
    {
        return URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $this->subscriber->id]);
    }
}
