<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    private string $baseUrl;

    /**
     * Set by verifyPaymentSignature() so the controller can read the capture
     * id it produced without a second API round trip — same pattern as
     * StripeGateway::paymentIntentIdFor(), just stashed instead of re-fetched
     * because capturing an order is a one-shot action, not idempotently
     * re-queryable like retrieving a Stripe session is.
     */
    private ?string $lastCaptureId = null;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.sandbox', true)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function name(): string
    {
        return 'paypal';
    }

    /**
     * Not INR — see the interface docblock. This sandbox account (and
     * India-registered PayPal business accounts generally) rejects INR
     * orders outright with a CURRENCY_NOT_SUPPORTED error.
     */
    public function currency(): string
    {
        return 'USD';
    }

    /**
     * Creates a PayPal Order (Orders API v2, intent=CAPTURE) and returns its
     * "approve" link — like Stripe Checkout, this is a hosted redirect flow:
     * PayPal owns the buyer-facing page, we only ever talk to its REST API
     * server-to-server. $meta['success_url']/['cancel_url'] are required.
     */
    public function createOrder(float $amount, string $currency, array $meta = []): array
    {
        $body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $meta['receipt'] ?? null,
                'custom_id' => (string) ($meta['notes']['payment_id'] ?? ''),
                'amount' => [
                    'currency_code' => strtoupper($currency),
                    'value' => number_format($amount, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => $meta['success_url'],
                'cancel_url' => $meta['cancel_url'],
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];

        $merchantId = config('services.paypal.merchant_id');
        if ($merchantId) {
            $body['purchase_units'][0]['payee'] = ['merchant_id' => $merchantId];
        }

        $response = Http::withToken($this->accessToken())
            ->post($this->baseUrl.'/v2/checkout/orders', $body)
            ->throw();

        $data = $response->json();
        $approveUrl = collect($data['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'gateway_order_id' => $data['id'],
            'raw' => array_merge($data, ['approve_url' => $approveUrl]),
        ];
    }

    /**
     * PayPal has no client-supplied signature to check (unlike Razorpay's
     * widget) — trust comes from capturing the order server-to-server with
     * our own credentials and checking the result. This is also the only
     * point the payment is actually charged, so it's called exactly once,
     * from the return-URL callback (never speculatively).
     */
    public function verifyPaymentSignature(array $payload): bool
    {
        $orderId = $payload['paypal_order_id'] ?? null;

        if (!$orderId) {
            return false;
        }

        try {
            $response = Http::withToken($this->accessToken())
                ->post($this->baseUrl."/v2/checkout/orders/{$orderId}/capture")
                ->throw();

            $data = $response->json();
            $this->lastCaptureId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            return ($data['status'] ?? null) === 'COMPLETED';
        } catch (RequestException $e) {
            Log::warning('PayPal: order capture failed during verify: '.$e->getMessage());

            return false;
        }
    }

    public function lastCaptureId(): ?string
    {
        return $this->lastCaptureId;
    }

    /**
     * PayPal's verify-webhook-signature call needs five transmission headers
     * plus the configured webhook id (from Dashboard > Webhooks, distinct
     * from the client id/secret) — not a single HMAC header like Razorpay/Stripe.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $webhookId = config('services.paypal.webhook_id');

        if (empty($webhookId)) {
            Log::error('PayPal: webhook id is not configured; refusing to trust webhook');

            return false;
        }

        $transmissionId = $request->header('PAYPAL-TRANSMISSION-ID');
        $transmissionTime = $request->header('PAYPAL-TRANSMISSION-TIME');
        $certUrl = $request->header('PAYPAL-CERT-URL');
        $authAlgo = $request->header('PAYPAL-AUTH-ALGO');
        $transmissionSig = $request->header('PAYPAL-TRANSMISSION-SIG');

        if (!$transmissionId || !$transmissionTime || !$certUrl || !$authAlgo || !$transmissionSig) {
            Log::warning('PayPal: webhook missing required transmission headers');

            return false;
        }

        try {
            $response = Http::withToken($this->accessToken())
                ->post($this->baseUrl.'/v1/notifications/verify-webhook-signature', [
                    'transmission_id' => $transmissionId,
                    'transmission_time' => $transmissionTime,
                    'cert_url' => $certUrl,
                    'auth_algo' => $authAlgo,
                    'transmission_sig' => $transmissionSig,
                    'webhook_id' => $webhookId,
                    'webhook_event' => json_decode($request->getContent(), true),
                ])
                ->throw();

            return $response->json('verification_status') === 'SUCCESS';
        } catch (RequestException $e) {
            Log::warning('PayPal: webhook signature verification request failed: '.$e->getMessage());

            return false;
        }
    }

    public function parseWebhookEvent(array $payload): array
    {
        $type = $payload['event_type'] ?? null;
        $resource = $payload['resource'] ?? [];

        return match ($type) {
            'CHECKOUT.ORDER.APPROVED' => [
                // Approval alone isn't payment — capture only happens via the
                // return-URL callback (verifyPaymentSignature above). This event
                // exists mainly so an unexpected order state is still logged.
                'event' => 'unknown',
                'gateway_order_id' => $resource['id'] ?? null,
                'gateway_payment_id' => null,
                'reason' => null,
            ],
            'PAYMENT.CAPTURE.COMPLETED' => [
                'event' => 'paid',
                'gateway_order_id' => $resource['supplementary_data']['related_ids']['order_id'] ?? null,
                'gateway_payment_id' => $resource['id'] ?? null,
                'reason' => null,
            ],
            'PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.DECLINED' => [
                'event' => 'failed',
                'gateway_order_id' => $resource['supplementary_data']['related_ids']['order_id'] ?? null,
                'gateway_payment_id' => $resource['id'] ?? null,
                'reason' => 'Payment declined',
            ],
            'CHECKOUT.ORDER.VOIDED' => [
                'event' => 'failed',
                'gateway_order_id' => $resource['id'] ?? null,
                'gateway_payment_id' => null,
                'reason' => 'Order voided',
            ],
            default => [
                'event' => 'unknown',
                'gateway_order_id' => $resource['id'] ?? null,
                'gateway_payment_id' => null,
                'reason' => null,
            ],
        };
    }

    /**
     * OAuth2 client-credentials token — required on every REST call. Cached
     * just under PayPal's own expires_in (normally ~9 hours) so a request
     * mid-checkout never straddles an expiry.
     */
    private function accessToken(): string
    {
        $cacheKey = 'paypal_access_token_'.substr(md5(config('services.paypal.client_id')), 0, 8);

        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $response = Http::asForm()
            ->withBasicAuth(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            ->post($this->baseUrl.'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->throw();

        $token = $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in', 3600);

        Cache::put($cacheKey, $token, max($expiresIn - 60, 60));

        return $token;
    }
}
