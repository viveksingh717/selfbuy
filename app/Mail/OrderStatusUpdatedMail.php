<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent from the admin order page: current order status, plus tracking details when set. */
class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        $label = [
            'pending'    => 'received',
            'processing' => 'being prepared',
            'shipped'    => 'on its way',
            'delivered'  => 'delivered',
            'cancelled'  => 'cancelled',
        ][$this->order->order_status] ?? 'updated';

        return new Envelope(
            subject: "Your order #{$this->order->order_number} is {$label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.order_status_updated',
            with: [
                'order'    => $this->order->loadMissing('items'),
                'orderUrl' => $this->order->viewUrl(), // signed - opens without logging in
            ],
        );
    }
}
