<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstamojoGateway implements PaymentGatewayInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.instamojo.sandbox', true)
            ? 'https://test.instamojo.com/api/1.1'
            : 'https://www.instamojo.com/api/1.1';
    }

    public function name(): string
    {
        return 'instamojo';
    }

    public function currency(): string
    {
        // Instamojo is India-only and only ever settles in INR — unlike
        // PayPal, no conversion is needed here.
        return 'INR';
    }

    /**
     * Creates a Payment Request and returns its hosted checkout link
     * ("longurl") — like Stripe/PayPal, this is a redirect flow, not an
     * in-page widget. Unlike those two, Instamojo's API only takes a single
     * redirect_url (no separate success/cancel) — both outcomes land back
     * on the same URL with a payment_status query param, handled in
     * InstamojoController::callback().
     */
    public function createOrder(float $amount, string $currency, array $meta = []): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->asForm()
            ->post($this->baseUrl.'/payment-requests/', array_filter([
                'purpose' => 'Order '.($meta['receipt'] ?? ''),
                'amount' => number_format($amount, 2, '.', ''),
                'buyer_name' => $meta['buyer_name'] ?? null,
                'email' => $meta['buyer_email'] ?? null,
                'phone' => $meta['buyer_phone'] ?? null,
                'redirect_url' => $meta['success_url'],
                'webhook' => $meta['webhook_url'] ?? null,
                'allow_repeated_payments' => false,
                'send_email' => false,
                'send_sms' => false,
            ]))
            ->throw();

        $data = $response->json('payment_request');

        return [
            'gateway_order_id' => $data['id'],
            'raw' => array_merge($data, ['approve_url' => $data['longurl'] ?? null]),
        ];
    }

    /**
     * Instamojo's redirect back includes payment_id/payment_request_id but
     * (depending on integration mode) not always a trustworthy signature —
     * so, same as Stripe/PayPal, trust comes from fetching the payment
     * directly from Instamojo's API with our own credentials and checking
     * its actual status, never the redirect query params alone.
     */
    public function verifyPaymentSignature(array $payload): bool
    {
        $paymentId = $payload['instamojo_payment_id'] ?? null;

        if (!$paymentId) {
            return false;
        }

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get($this->baseUrl."/payments/{$paymentId}/")
                ->throw();

            return $response->json('payment.status') === 'Credit';
        } catch (RequestException $e) {
            Log::warning('Instamojo: payment lookup failed during verify: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Instamojo webhooks are form-encoded with a `mac` field: HMAC-SHA1 over
     * every other field, sorted by key and joined with '|', keyed with the
     * account's private salt (config('services.instamojo.salt') — distinct
     * from the API key/auth token used for authenticated requests).
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $salt = config('services.instamojo.salt');

        if (empty($salt)) {
            Log::error('Instamojo: salt is not configured; refusing to trust webhook');

            return false;
        }

        $data = $request->all();
        $mac = $data['mac'] ?? null;

        if (!$mac) {
            return false;
        }

        unset($data['mac']);
        ksort($data);
        $message = implode('|', array_map(static fn ($v) => (string) $v, $data));
        $computed = hash_hmac('sha1', $message, $salt);

        return hash_equals($computed, (string) $mac);
    }

    public function parseWebhookEvent(array $payload): array
    {
        $status = $payload['status'] ?? null;

        return match ($status) {
            'Credit' => [
                'event' => 'paid',
                'gateway_order_id' => $payload['payment_request_id'] ?? null,
                'gateway_payment_id' => $payload['payment_id'] ?? null,
                'reason' => null,
            ],
            'Failed' => [
                'event' => 'failed',
                'gateway_order_id' => $payload['payment_request_id'] ?? null,
                'gateway_payment_id' => $payload['payment_id'] ?? null,
                'reason' => 'Payment failed',
            ],
            default => [
                'event' => 'unknown',
                'gateway_order_id' => $payload['payment_request_id'] ?? null,
                'gateway_payment_id' => $payload['payment_id'] ?? null,
                'reason' => null,
            ],
        };
    }

    private function authHeaders(): array
    {
        return [
            'X-Api-Key' => config('services.instamojo.api_key'),
            'X-Auth-Token' => config('services.instamojo.auth_token'),
        ];
    }
}
