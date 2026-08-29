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

class PayPalController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private CheckoutAccountService $checkoutAccountService,
    ) {
    }

    /**
     * PayPal Checkout is a hosted page — like Stripe, just an immediate
     * redirect to the "approve" link captured in $payment->meta when the
     * order was created (see PaymentService::initiate()).
     */
    public function show(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'paypal') {
            abort(404);
        }

        if ($payment->status === 'paid' && $payment->order) {
            return redirect()->route('checkout.success', $payment->order->order_number);
        }

        if ($payment->status !== 'created' || empty($payment->meta['approve_url'])) {
            return redirect()->route('checkout.index')->with('error', 'This payment session is no longer active. Please try again.');
        }

        return redirect()->away($payment->meta['approve_url']);
    }

    /**
     * PayPal redirects the browser here after the buyer approves the order
     * (?token={order_id}&PayerID=...). The order id is never trusted on its
     * own — verifyPaymentSignature() captures it server-to-server with our
     * own credentials, which is also the only point money actually moves.
     */
    public function callback(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'paypal') {
            abort(404);
        }

        if ($payment->status === 'paid' && $payment->order) {
            return redirect()->route('checkout.success', $payment->order->order_number);
        }

        $orderId = $request->query('token');

        if (!$orderId || $orderId !== $payment->gateway_order_id) {
            Log::warning('PayPal: callback token mismatch', ['payment_id' => $payment->id]);

            return redirect()->route('checkout.index')->with('error', 'Payment could not be verified. If money was deducted, it will be refunded automatically.');
        }

        $gateway = $this->paymentService->gateway('paypal');

        if (!$gateway->verifyPaymentSignature(['paypal_order_id' => $orderId])) {
            $this->paymentService->markFailed($payment, 'Payment not completed', [], true);

            return redirect()->route('checkout.index')->with('error', 'Payment was not completed.');
        }

        $result = $this->paymentService->completePayment($payment, $gateway->lastCaptureId() ?? $orderId, null, ['source' => 'client_verify']);

        if (!$result['status']) {
            return redirect()->route('checkout.index')->with('error', $result['message']);
        }

        $order = $result['data'];
        $this->checkoutAccountService->maybeCreateAccount($order, $payment->billing_data);

        return redirect()->route('checkout.success', $order->order_number);
    }

    /**
     * Hit when the buyer clicks PayPal's own "Cancel and return" link — the
     * one client-side signal PayPal gives for "gave up", distinct from a
     * webhook report.
     */
    public function cancel(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'paypal') {
            abort(404);
        }

        $this->paymentService->markFailed($payment, 'Cancelled at PayPal Checkout', [], true);

        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled.');
    }

    /**
     * Server-to-server webhook. No session/CSRF context exists here, so
     * ownership isn't checked — only the gateway's own signature.
     */
    public function webhook(Request $request, ResponseService $rs)
    {
        $gateway = $this->paymentService->gateway('paypal');

        if (!$gateway->verifyWebhookSignature($request)) {
            Log::warning('PayPal: webhook signature invalid or missing');

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = json_decode($request->getContent(), true) ?? [];
        $parsed = $gateway->parseWebhookEvent($payload);

        Log::info('PayPal: webhook received', ['type' => $payload['event_type'] ?? 'unknown', 'parsed_event' => $parsed['event'], 'gateway_order_id' => $parsed['gateway_order_id']]);

        if (!$parsed['gateway_order_id']) {
            return response()->json(['message' => 'Ignored'], 200);
        }

        $payment = Payment::where('gateway_order_id', $parsed['gateway_order_id'])
            ->where('gateway', 'paypal')
            ->first();

        if (!$payment) {
            Log::warning('PayPal: webhook referenced an unknown payment', ['gateway_order_id' => $parsed['gateway_order_id']]);

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

    private function ownsPayment(Request $request, Payment $payment): bool
    {
        return Auth::guard('web')->check()
            ? $payment->user_id === Auth::guard('web')->id()
            : $payment->session_id === Session::getId();
    }
}
