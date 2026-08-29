<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\CheckoutAccountService;
use App\Services\Payments\PaymentService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StripeController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private CheckoutAccountService $checkoutAccountService,
    ) {
    }

    /**
     * Stripe Checkout is a hosted page — unlike Razorpay's in-page widget,
     * there's nothing to render here, just an immediate redirect to the
     * Checkout Session URL that was captured in $payment->meta when the
     * session was created (see PaymentService::initiate()).
     */
    public function show(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'stripe') {
            abort(404);
        }

        if ($payment->status === 'paid' && $payment->order) {
            return redirect()->route('checkout.success', $payment->order->order_number);
        }

        if ($payment->status !== 'created' || empty($payment->meta['url'])) {
            return redirect()->route('checkout.index')->with('error', 'This payment session is no longer active. Please try again.');
        }

        return redirect()->away($payment->meta['url']);
    }

    /**
     * Stripe redirects the browser here after a successful payment. The
     * session_id it returns is never trusted on its own — verifyPaymentSignature()
     * re-fetches the session from Stripe's API with our secret key and checks
     * its actual payment_status before anything is created.
     */
    public function callback(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'stripe') {
            abort(404);
        }

        if ($payment->status === 'paid' && $payment->order) {
            return redirect()->route('checkout.success', $payment->order->order_number);
        }

        $sessionId = $request->query('session_id');

        if (!$sessionId || $sessionId !== $payment->gateway_order_id) {
            Log::warning('Stripe: callback session_id mismatch', ['payment_id' => $payment->id]);

            return redirect()->route('checkout.index')->with('error', 'Payment could not be verified. If money was deducted, it will be refunded automatically.');
        }

        $gateway = $this->paymentService->gateway('stripe');

        if (!$gateway->verifyPaymentSignature(['stripe_session_id' => $sessionId])) {
            $this->paymentService->markFailed($payment, 'Payment not completed', [], true);

            return redirect()->route('checkout.index')->with('error', 'Payment was not completed.');
        }

        $paymentIntentId = $gateway->paymentIntentIdFor($sessionId);

        $result = $this->paymentService->completePayment($payment, $paymentIntentId ?? $sessionId, null, ['source' => 'client_verify']);

        if (!$result['status']) {
            return redirect()->route('checkout.index')->with('error', $result['message']);
        }

        $order = $result['data'];
        $this->checkoutAccountService->maybeCreateAccount($order, $payment->billing_data);

        return redirect()->route('checkout.success', $order->order_number);
    }

    /**
     * Hit when the customer clicks Stripe Checkout's own back link — the one
     * client-side signal Stripe gives us for "gave up", distinct from a webhook
     * report. Declined cards are retried entirely within Stripe's hosted page
     * and never reach this route.
     */
    public function cancel(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'stripe') {
            abort(404);
        }

        $this->paymentService->markFailed($payment, 'Cancelled at Stripe Checkout', [], true);

        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled.');
    }

    /**
     * Server-to-server webhook. No session/CSRF context exists here, so
     * ownership isn't checked — only the gateway's own signature.
     */
    public function webhook(Request $request, ResponseService $rs)
    {
        $gateway = $this->paymentService->gateway('stripe');

        if (!$gateway->verifyWebhookSignature($request)) {
            Log::warning('Stripe: webhook signature invalid or missing');

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = json_decode($request->getContent(), true) ?? [];
        $parsed = $gateway->parseWebhookEvent($payload);

        Log::info('Stripe: webhook received', ['type' => $payload['type'] ?? 'unknown', 'parsed_event' => $parsed['event'], 'gateway_order_id' => $parsed['gateway_order_id']]);

        if (!$parsed['gateway_order_id']) {
            return response()->json(['message' => 'Ignored'], 200);
        }

        $payment = Payment::where('gateway_order_id', $parsed['gateway_order_id'])
            ->where('gateway', 'stripe')
            ->first();

        if (!$payment) {
            Log::warning('Stripe: webhook referenced an unknown payment', ['gateway_order_id' => $parsed['gateway_order_id']]);

            return response()->json(['message' => 'Unknown payment'], 200);
        }

        if ($parsed['event'] === 'paid') {
            $result = $this->paymentService->completePayment($payment, $parsed['gateway_payment_id'] ?? $parsed['gateway_order_id'], null, ['source' => 'webhook']);

            if ($result['status']) {
                $this->checkoutAccountService->maybeCreateAccount($result['data'], $payment->billing_data);
            }
        } elseif ($parsed['event'] === 'failed') {
            $this->paymentService->markFailed($payment, $parsed['reason'] ?? 'Payment failed', ['gateway_payment_id' => $parsed['gateway_payment_id']]);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    private function retrievePaymentIntentId(string $sessionId): ?string
    {
        try {
            $session = app(\Stripe\StripeClient::class, ['apiKey' => config('services.stripe.secret')])
                ->checkout->sessions->retrieve($sessionId);

            return $session->payment_intent;
        } catch (\Throwable $e) {
            Log::warning('Stripe: could not retrieve payment_intent for session: '.$e->getMessage());

            return null;
        }
    }

    private function ownsPayment(Request $request, Payment $payment): bool
    {
        return Auth::guard('web')->check()
            ? $payment->user_id === Auth::guard('web')->id()
            : $payment->session_id === Session::getId();
    }
}
