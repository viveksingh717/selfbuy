<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\PaymentFailedMail;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderNotificationService
{
    public function __construct(private SmsService $smsService)
    {
    }

    public function sendOrderConfirmation(Order $order): void
    {
        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
            Log::info('Order notification: confirmation email sent', ['order_id' => $order->id, 'to' => $order->email]);
        } catch (Throwable $e) {
            Log::error('Order notification: confirmation email failed: '.$e->getMessage(), ['order_id' => $order->id]);
        }

        if ($order->phone) {
            $sent = $this->smsService->sendMessage(
                $order->phone,
                "Your {$this->storeName()} order #{$order->order_number} for ".$this->rupees($order->total)." is confirmed. Thank you for shopping with us!",
            );

            Log::info('Order notification: confirmation SMS '.($sent ? 'sent' : 'failed'), ['order_id' => $order->id, 'phone' => $order->phone]);
        }
    }

    /**
     * Deliberately not called for every individual failed payment attempt —
     * a customer retrying a declined card within the same checkout session
     * shouldn't get an email per attempt. Only called once the customer has
     * genuinely stepped away (see PaymentService::markFailed()'s $notifyCustomer flag).
     */
    public function sendPaymentFailedNotification(Payment $payment): void
    {
        $email = $payment->billing_data['email'] ?? null;
        $phone = $payment->billing_data['phone'] ?? null;

        if ($email) {
            try {
                Mail::to($email)->send(new PaymentFailedMail($payment));
                Log::info('Order notification: payment-failed email sent', ['payment_id' => $payment->id, 'to' => $email]);
            } catch (Throwable $e) {
                Log::error('Order notification: payment-failed email failed: '.$e->getMessage(), ['payment_id' => $payment->id]);
            }
        }

        if ($phone) {
            $sent = $this->smsService->sendMessage(
                $phone,
                "Your {$this->storeName()} payment of ".$this->rupees($payment->amount)." could not be completed. Your cart is still saved — please try again.",
            );

            Log::info('Order notification: payment-failed SMS '.($sent ? 'sent' : 'failed'), ['payment_id' => $payment->id, 'phone' => $phone]);
        }
    }

    private function rupees(float|string $amount): string
    {
        return '₹'.number_format((float) $amount, 2);
    }

    private function storeName(): string
    {
        return config('app.name');
    }
}
