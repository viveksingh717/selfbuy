<?php

namespace App\Services\Payments\Contracts;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * The gateway's identifier, e.g. 'razorpay'. Matches Payment::gateway.
     */
    public function name(): string;

    /**
     * The currency this gateway actually charges in. Almost always the
     * store's own currency (INR) — PayPal is the current exception, since
     * its India-registered accounts can't receive INR at all. PaymentService
     * converts the cart total to this currency before calling createOrder().
     */
    public function currency(): string;

    /**
     * Create a payment intent/order with the gateway for the given amount.
     * $amount is a decimal in the store's currency units (e.g. rupees), not the
     * gateway's smallest unit — each implementation converts internally.
     *
     * @return array{gateway_order_id: string, raw: array}
     */
    public function createOrder(float $amount, string $currency, array $meta = []): array;

    /**
     * Verify a client-side payment callback's signature/payload.
     * $payload carries whatever fields the gateway's checkout flow returns
     * (e.g. razorpay_order_id, razorpay_payment_id, razorpay_signature).
     */
    public function verifyPaymentSignature(array $payload): bool;

    /**
     * Verify a server-to-server webhook request's signature. Takes the whole
     * Request (not just a body+header pair) because gateways differ in how many
     * headers the check needs — Razorpay/Stripe use a single signature header,
     * PayPal's verify-webhook-signature call needs five.
     */
    public function verifyWebhookSignature(Request $request): bool;

    /**
     * Normalize a webhook payload into a common shape so PaymentService doesn't
     * need to know each gateway's event/field naming.
     *
     * @return array{event: 'paid'|'failed'|'unknown', gateway_order_id: ?string, gateway_payment_id: ?string, reason: ?string}
     */
    public function parseWebhookEvent(array $payload): array;
}
