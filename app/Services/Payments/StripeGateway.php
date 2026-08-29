<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function name(): string
    {
        return 'stripe';
    }

    public function currency(): string
    {
        return 'INR';
    }

    /**
     * Creates a hosted Checkout Session rather than a PaymentIntent — Stripe
     * fully owns the card form and 3D Secure/SCA challenge, so nothing
     * card-data-handling ever touches our server. $meta['success_url'] and
     * $meta['cancel_url'] are required; $meta['payment_id'] is threaded onto
     * the session so the webhook/return handler can look the Payment back up.
     */
    public function createOrder(float $amount, string $currency, array $meta = []): array
    {
        $session = $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'unit_amount' => (int) round($amount * 100), // Stripe expects the smallest currency unit (paise for INR).
                    'product_data' => ['name' => 'Order '.($meta['receipt'] ?? '')],
                ],
                'quantity' => 1,
            ]],
            'success_url' => $meta['success_url'].'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $meta['cancel_url'],
            'client_reference_id' => (string) ($meta['notes']['payment_id'] ?? ''),
            'metadata' => $meta['notes'] ?? [],
        ]);

        return [
            'gateway_order_id' => $session->id,
            'raw' => $session->toArray(),
        ];
    }

    /**
     * Stripe Checkout has no client-supplied signature to check (unlike
     * Razorpay's widget) — the session id the browser returns with is only
     * ever used to look the Payment row back up. Trust instead comes from
     * retrieving the session directly from Stripe's API with our secret key
     * and checking its actual payment_status server-side.
     */
    public function verifyPaymentSignature(array $payload): bool
    {
        $sessionId = $payload['stripe_session_id'] ?? null;

        if (!$sessionId) {
            return false;
        }

        try {
            $session = $this->client->checkout->sessions->retrieve($sessionId);

            return $session->payment_status === 'paid';
        } catch (ApiErrorException $e) {
            Log::warning('Stripe: session retrieval failed during verify: '.$e->getMessage());

            return false;
        }
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $secret = config('services.stripe.webhook_secret');

        if (empty($secret)) {
            Log::error('Stripe: webhook secret is not configured; refusing to trust webhook');

            return false;
        }

        $signatureHeader = $request->header('Stripe-Signature', '');

        if (!$signatureHeader) {
            return false;
        }

        try {
            Webhook::constructEvent($request->getContent(), $signatureHeader, $secret);

            return true;
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe: webhook signature verification failed: '.$e->getMessage());

            return false;
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe: webhook payload invalid: '.$e->getMessage());

            return false;
        }
    }

    /**
     * A Checkout Session's payment_intent id isn't known until the payment
     * actually completes, so the callback route re-fetches it here to store
     * as Payment::gateway_payment_id (the client-returned session_id is
     * already used as gateway_order_id and shouldn't be duplicated).
     */
    public function paymentIntentIdFor(string $sessionId): ?string
    {
        try {
            return $this->client->checkout->sessions->retrieve($sessionId)->payment_intent;
        } catch (ApiErrorException $e) {
            Log::warning('Stripe: could not retrieve payment_intent for session: '.$e->getMessage());

            return null;
        }
    }

    public function parseWebhookEvent(array $payload): array
    {
        $type = $payload['type'] ?? null;
        $session = $payload['data']['object'] ?? [];

        return match ($type) {
            'checkout.session.completed', 'checkout.session.async_payment_succeeded' => [
                'event' => ($session['payment_status'] ?? null) === 'paid' ? 'paid' : 'unknown',
                'gateway_order_id' => $session['id'] ?? null,
                'gateway_payment_id' => $session['payment_intent'] ?? null,
                'reason' => null,
            ],
            'checkout.session.async_payment_failed' => [
                'event' => 'failed',
                'gateway_order_id' => $session['id'] ?? null,
                'gateway_payment_id' => $session['payment_intent'] ?? null,
                'reason' => 'Payment failed',
            ],
            'checkout.session.expired' => [
                'event' => 'failed',
                'gateway_order_id' => $session['id'] ?? null,
                'gateway_payment_id' => null,
                'reason' => 'Checkout session expired',
            ],
            default => [
                'event' => 'unknown',
                'gateway_order_id' => $session['id'] ?? null,
                'gateway_payment_id' => $session['payment_intent'] ?? null,
                'reason' => null,
            ],
        };
    }
}
