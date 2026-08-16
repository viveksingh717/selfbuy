<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayGateway implements PaymentGatewayInterface
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret'),
        );
    }

    public function name(): string
    {
        return 'razorpay';
    }

    public function createOrder(float $amount, string $currency, array $meta = []): array
    {
        $order = $this->api->order->create([
            'amount' => (int) round($amount * 100), // Razorpay expects the smallest currency unit (paise for INR).
            'currency' => $currency,
            'receipt' => $meta['receipt'] ?? null,
            'payment_capture' => 1,
            'notes' => $meta['notes'] ?? [],
        ]);

        return [
            'gateway_order_id' => $order->id,
            'raw' => $order->toArray(),
        ];
    }

    public function verifyPaymentSignature(array $payload): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $payload['razorpay_order_id'] ?? null,
                'razorpay_payment_id' => $payload['razorpay_payment_id'] ?? null,
                'razorpay_signature' => $payload['razorpay_signature'] ?? null,
            ]);

            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay: payment signature verification failed: '.$e->getMessage());

            return false;
        }
    }

    public function verifyWebhookSignature(string $rawBody, string $signatureHeader): bool
    {
        $secret = config('services.razorpay.webhook_secret');

        if (empty($secret)) {
            Log::error('Razorpay: webhook secret is not configured; refusing to trust webhook');

            return false;
        }

        try {
            $this->api->utility->verifyWebhookSignature($rawBody, $signatureHeader, $secret);

            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay: webhook signature verification failed: '.$e->getMessage());

            return false;
        }
    }

    public function parseWebhookEvent(array $payload): array
    {
        $event = $payload['event'] ?? null;
        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];

        return match ($event) {
            'payment.captured', 'order.paid' => [
                'event' => 'paid',
                'gateway_order_id' => $paymentEntity['order_id'] ?? null,
                'gateway_payment_id' => $paymentEntity['id'] ?? null,
                'reason' => null,
            ],
            'payment.failed' => [
                'event' => 'failed',
                'gateway_order_id' => $paymentEntity['order_id'] ?? null,
                'gateway_payment_id' => $paymentEntity['id'] ?? null,
                'reason' => $paymentEntity['error_description'] ?? 'Payment failed',
            ],
            default => [
                'event' => 'unknown',
                'gateway_order_id' => $paymentEntity['order_id'] ?? null,
                'gateway_payment_id' => $paymentEntity['id'] ?? null,
                'reason' => null,
            ],
        };
    }
}
