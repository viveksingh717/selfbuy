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
use Illuminate\Support\Facades\Validator;

class RazorpayController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private CheckoutAccountService $checkoutAccountService,
    ) {
    }

    /**
     * The page that opens the Razorpay Checkout widget for a payment created
     * during checkout. Route-model-bound; ownership is still re-checked since
     * a numeric ID is guessable.
     */
    public function show(Request $request, Payment $payment)
    {
        if (!$this->ownsPayment($request, $payment) || $payment->gateway !== 'razorpay') {
            abort(404);
        }

        if ($payment->status === 'paid' && $payment->order) {
            return redirect()->route('checkout.success', $payment->order->order_number);
        }

        if ($payment->status !== 'created') {
            return redirect()->route('checkout.index')->with('error', 'This payment session is no longer active. Please try again.');
        }

        return view('shop.payment.razorpay', [
            'payment' => $payment,
            'razorpayKey' => config('services.razorpay.key_id'),
        ]);
    }

    /**
     * Client-side success callback from Razorpay Checkout. Verifies the
     * cryptographic signature server-side before ever creating an order —
     * the client-supplied payment ID is never trusted on its own.
     */
    public function verify(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $payment = Payment::where('gateway_order_id', $request->razorpay_order_id)
            ->where('gateway', 'razorpay')
            ->first();

        if (!$payment || !$this->ownsPayment($request, $payment)) {
            Log::warning('Razorpay: verify called for unknown/unowned order', ['gateway_order_id' => $request->razorpay_order_id]);

            return $rs->setErrorResponse('Payment session not found.');
        }

        $gateway = $this->paymentService->gateway('razorpay');

        if (!$gateway->verifyPaymentSignature($request->only(['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature']))) {
            $this->paymentService->markFailed($payment, 'Signature verification failed', ['gateway_payment_id' => $request->razorpay_payment_id]);

            return $rs->setErrorResponse('Payment could not be verified. If money was deducted, it will be refunded automatically.');
        }

        $result = $this->paymentService->completePayment(
            $payment,
            $request->razorpay_payment_id,
            $request->razorpay_signature,
            ['source' => 'client_verify'],
        );

        if (!$result['status']) {
            return $rs->setErrorResponse($result['message']);
        }

        $order = $result['data'];
        $this->checkoutAccountService->maybeCreateAccount($order, $payment->billing_data);

        return $rs->setSuccessResponse('Payment successful!', [
            'redirect' => route('checkout.success', $order->order_number),
        ]);
    }

    /**
     * Client reports the widget was dismissed or Razorpay returned a failure —
     * lets the payment be marked failed promptly instead of sitting as
     * indefinitely "created" until an (optional) webhook eventually arrives.
     *
     * `abandoned` distinguishes a genuine give-up (the widget closed, sent only
     * from the JS ondismiss handler) from an individual declined attempt the
     * customer might still retry within the same Checkout session — only the
     * former sends a "payment failed" notification.
     */
    public function failed(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'gateway_order_id' => 'required|string',
            'reason' => 'nullable|string|max:500',
            'abandoned' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $payment = Payment::where('gateway_order_id', $request->gateway_order_id)
            ->where('gateway', 'razorpay')
            ->first();

        if (!$payment || !$this->ownsPayment($request, $payment)) {
            return $rs->setErrorResponse('Payment session not found.');
        }

        $this->paymentService->markFailed(
            $payment,
            $request->input('reason', 'Cancelled or failed at checkout'),
            [],
            $request->boolean('abandoned'),
        );

        return $rs->setSuccessResponse('Payment cancelled.', []);
    }

    /**
     * Server-to-server webhook. Trusted only via the signed request body — no
     * session/CSRF context exists here, so ownership isn't checked, only the
     * gateway's own signature. Always acknowledges quickly; Razorpay retries
     * on non-2xx.
     */
    public function webhook(Request $request, ResponseService $rs)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        $gateway = $this->paymentService->gateway('razorpay');

        if (!$signature || !$gateway->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('Razorpay: webhook signature invalid or missing');

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payload = json_decode($rawBody, true) ?? [];
        $parsed = $gateway->parseWebhookEvent($payload);

        Log::info('Razorpay: webhook received', ['event' => $payload['event'] ?? 'unknown', 'parsed_event' => $parsed['event'], 'gateway_order_id' => $parsed['gateway_order_id']]);

        if (!$parsed['gateway_order_id']) {
            return response()->json(['message' => 'Ignored'], 200);
        }

        $payment = Payment::where('gateway_order_id', $parsed['gateway_order_id'])
            ->where('gateway', 'razorpay')
            ->first();

        if (!$payment) {
            Log::warning('Razorpay: webhook referenced an unknown payment', ['gateway_order_id' => $parsed['gateway_order_id']]);

            return response()->json(['message' => 'Unknown payment'], 200);
        }

        if ($parsed['event'] === 'paid' && $parsed['gateway_payment_id']) {
            $result = $this->paymentService->completePayment($payment, $parsed['gateway_payment_id'], null, ['source' => 'webhook']);

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
