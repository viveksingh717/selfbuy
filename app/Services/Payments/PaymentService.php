<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Services\CartService;
use App\Services\OrderNotificationService;
use App\Services\OrderService;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Throwable;

class PaymentService
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private OrderNotificationService $orderNotificationService,
    ) {
    }

    public function gateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'razorpay' => app(RazorpayGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$name}"),
        };
    }

    /**
     * Start a gateway payment: snapshot the current cart total and the checkout
     * form's billing data into a Payment row, then create the matching order
     * with the gateway. No Order is created yet — that only happens once the
     * payment is verified as successful (see completePayment()).
     */
    public function initiate(string $gatewayName, array $billingData): array
    {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return ['status' => false, 'message' => 'Your cart is empty'];
        }

        $items = $cartItems->map(fn ($item) => $this->orderService->normalizeCartItem($item))->all();
        $totals = $this->cartService->getTotals();
        $gateway = $this->gateway($gatewayName);

        $payment = Payment::create([
            'user_id' => Auth::guard('web')->id(),
            'session_id' => Session::getId(),
            'gateway' => $gatewayName,
            'amount' => $totals['cart_total'],
            'currency' => 'INR',
            'status' => 'created',
            'billing_data' => $billingData,
            // The request that later completes this payment (a webhook, in
            // particular) may have no session or live cart of its own — the
            // order is built from this snapshot instead.
            'order_snapshot' => ['items' => $items, 'totals' => $totals],
        ]);

        try {
            $result = $gateway->createOrder($totals['cart_total'], 'INR', [
                'receipt' => 'payment_'.$payment->id,
                'notes' => ['payment_id' => $payment->id],
            ]);
        } catch (Throwable $e) {
            Log::error("Payment ({$gatewayName}): order creation failed: ".$e->getMessage(), ['payment_id' => $payment->id]);
            $payment->update(['status' => 'failed', 'failure_reason' => 'Gateway order creation failed']);

            return ['status' => false, 'message' => 'Could not start payment. Please try again.'];
        }

        $payment->update([
            'gateway_order_id' => $result['gateway_order_id'],
            'meta' => $result['raw'],
        ]);

        Log::info("Payment ({$gatewayName}): order created", ['payment_id' => $payment->id, 'gateway_order_id' => $result['gateway_order_id'], 'amount' => $totals['cart_total']]);

        return ['status' => true, 'data' => $payment];
    }

    /**
     * Complete a payment after the gateway confirms success — called from both
     * the client-side verify callback and the webhook handler, so it must be
     * idempotent: whichever arrives first creates the order, the other is a no-op.
     */
    public function completePayment(Payment $payment, string $gatewayPaymentId, ?string $gatewaySignature, array $rawEvent = []): array
    {
        try {
            return Cache::lock("payment-complete:{$payment->id}", 15)->block(10, function () use ($payment, $gatewayPaymentId, $gatewaySignature, $rawEvent) {
                $payment->refresh();

                if ($payment->order_id) {
                    Log::info('Payment: already completed, ignoring duplicate callback', ['payment_id' => $payment->id, 'order_id' => $payment->order_id]);

                    return ['status' => true, 'data' => $payment->order];
                }

                // Note: a prior 'failed' status is NOT treated as terminal here. Razorpay
                // (and gateways generally) let a customer retry a failed attempt against
                // the same order — e.g. picking a different card after a decline, all
                // within one Checkout session. Only an already-created order (above) is
                // truly final; a failed status is just "most recent attempt didn't work".

                $snapshot = $payment->order_snapshot ?? [];

                $result = $this->orderService->placeOrderFromSnapshot(
                    $payment->billing_data,
                    $snapshot['items'] ?? [],
                    $snapshot['totals'] ?? [],
                    $payment->user_id,
                    $payment->session_id,
                    $payment->gateway,
                    'paid',
                );

                if (!$result['status']) {
                    Log::error('Payment: order creation failed after successful payment', ['payment_id' => $payment->id, 'message' => $result['message']]);
                    $payment->update([
                        'status' => 'failed',
                        'failure_reason' => $result['message'],
                        'gateway_payment_id' => $gatewayPaymentId,
                        'gateway_signature' => $gatewaySignature,
                    ]);

                    // Unlike a declined card, this is money the gateway actually captured
                    // that we then failed to turn into an order (e.g. stock ran out in the
                    // gap between payment and completion) — always worth telling the customer,
                    // not gated behind the "definitely abandoned" check markFailed() normally uses.
                    $this->orderNotificationService->sendPaymentFailedNotification($payment->fresh());

                    return $result;
                }

                $order = $result['data'];

                $payment->update([
                    'order_id' => $order->id,
                    'gateway_payment_id' => $gatewayPaymentId,
                    'gateway_signature' => $gatewaySignature,
                    'status' => 'paid',
                    'failure_reason' => null, // Clear any reason left over from an earlier failed attempt on this order.
                    'paid_at' => now(),
                    'meta' => array_merge($payment->meta ?? [], ['completed_via' => $rawEvent['source'] ?? 'verify']),
                ]);

                Log::info('Payment: completed and order created', ['payment_id' => $payment->id, 'order_id' => $order->id, 'gateway' => $payment->gateway]);

                return ['status' => true, 'data' => $order];
            });
        } catch (LockTimeoutException) {
            Log::warning('Payment: completion lock timed out', ['payment_id' => $payment->id]);

            return ['status' => false, 'message' => 'Please try again in a moment.'];
        }
    }

    /**
     * $notifyCustomer should only be true when the customer has genuinely stepped
     * away (e.g. closed the Checkout widget) — not for every individual declined
     * attempt, since Razorpay lets them retry with a different method in the same
     * session and a "payment failed" email per attempt would be noisy and alarming.
     */
    public function markFailed(Payment $payment, string $reason, array $ids = [], bool $notifyCustomer = false): void
    {
        if ($payment->order_id) {
            return; // Already succeeded — a late failure event must not overwrite that.
        }

        $payment->update(array_filter([
            'status' => 'failed',
            'failure_reason' => $reason,
            'gateway_payment_id' => $ids['gateway_payment_id'] ?? $payment->gateway_payment_id,
        ]));

        Log::info('Payment: marked failed', ['payment_id' => $payment->id, 'reason' => $reason, 'notify' => $notifyCustomer]);

        if ($notifyCustomer) {
            $this->orderNotificationService->sendPaymentFailedNotification($payment->fresh());
        }
    }
}
