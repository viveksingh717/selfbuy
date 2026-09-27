<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\CouponModel;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Admin-side order management: status workflow, payment status, shipment tracking,
 * customer emails and the activity timeline (order_histories).
 * Every method returns ['success' => bool, 'message' => string].
 */
class AdminOrderService
{
    /**
     * Move an order along its lifecycle (see Order::NEXT_STATUSES).
     *  - shipped / delivered / cancelled are time-stamped
     *  - delivered COD orders are marked paid (cash collected on delivery)
     *  - cancelling puts the stock and the coupon use back (once)
     */
    public function changeStatus(Order $order, string $to, ?string $note, bool $notify, ?int $adminId): array
    {
        $from = $order->order_status;

        if (!in_array($to, $order->nextStatuses(), true)) {
            return $this->fail("An order that is \"{$from}\" can't be changed to \"{$to}\".");
        }

        DB::transaction(function () use ($order, $from, $to, $note, $adminId) {
            $order->order_status = $to;

            if ($to === 'shipped') {
                $order->shipped_at = now();
            } elseif ($to === 'delivered') {
                $order->delivered_at = now();

                if ($order->payment_method === 'cod' && $order->payment_status === 'pending') {
                    $order->payment_status = 'paid';
                    $this->log($order, 'payment', 'pending', 'paid', 'Cash collected on delivery.', $adminId);
                }
            } elseif ($to === 'cancelled') {
                $order->cancelled_at = now();
                $order->cancel_reason = $note;
                $this->restoreStock($order);
            }

            $order->save();
            $this->log($order, 'order', $from, $to, $note, $adminId);
        });

        $message = 'Order marked as ' . ucfirst($to) . '.';
        if ($notify) {
            $message .= $this->notifyCustomer($order, $adminId)
                ? ' The customer has been emailed.'
                : ' (The email to the customer could not be sent - see the log.)';
        }

        return ['success' => true, 'message' => $message];
    }

    public function changePaymentStatus(Order $order, string $to, ?string $note, ?int $adminId): array
    {
        $from = $order->payment_status;

        if (!in_array($to, Order::PAYMENT_STATUSES, true)) {
            return $this->fail('Unknown payment status.');
        }
        if ($to === $from) {
            return $this->fail('Payment is already "' . $to . '".');
        }
        if ($to === 'refunded' && $from !== 'paid') {
            return $this->fail('Only a paid order can be marked as refunded.');
        }

        $order->update(['payment_status' => $to]);
        $this->log($order, 'payment', $from, $to, $note, $adminId);

        return ['success' => true, 'message' => 'Payment marked as ' . ucfirst($to) . '.'];
    }

    public function updateTracking(Order $order, array $data, bool $notify, ?int $adminId): array
    {
        if ($order->order_status === 'cancelled') {
            return $this->fail("A cancelled order can't have tracking details.");
        }

        $order->update([
            'tracking_courier' => $data['tracking_courier'] ?: null,
            'tracking_number'  => $data['tracking_number'] ?: null,
            'tracking_url'     => $data['tracking_url'] ?: null,
        ]);

        $summary = trim(($order->tracking_courier ?? '') . ' ' . ($order->tracking_number ?? '')) ?: 'Tracking details cleared';
        $this->log($order, 'tracking', null, null, $summary, $adminId);

        $message = 'Tracking details saved.';
        if ($notify) {
            $message .= $this->notifyCustomer($order, $adminId)
                ? ' The customer has been emailed.'
                : ' (The email to the customer could not be sent - see the log.)';
        }

        return ['success' => true, 'message' => $message];
    }

    /** $type: confirmation (resend the original order email) | status (current status + tracking). */
    public function sendEmail(Order $order, string $type, ?int $adminId): array
    {
        if ($type === 'confirmation') {
            try {
                Mail::to($order->email)->send(new OrderConfirmationMail($order->loadMissing('items')));
            } catch (Throwable $e) {
                Log::error('Admin: resend order confirmation failed: ' . $e->getMessage(), ['order_id' => $order->id]);

                return $this->fail('The email could not be sent. Please check the mail settings / log.');
            }

            $this->log($order, 'email', null, null, 'Order confirmation email re-sent.', $adminId, true);

            return ['success' => true, 'message' => 'Order confirmation re-sent to ' . $order->email . '.'];
        }

        return $this->notifyCustomer($order, $adminId)
            ? ['success' => true, 'message' => 'Status update sent to ' . $order->email . '.']
            : $this->fail('The email could not be sent. Please check the mail settings / log.');
    }

    public function addNote(Order $order, string $note, ?int $adminId): array
    {
        $this->log($order, 'note', null, null, $note, $adminId);

        return ['success' => true, 'message' => 'Note added.'];
    }

    /** Only cancelled orders can be deleted (their stock is already back on the shelf). */
    public function delete(Order $order): array
    {
        if ($order->order_status !== 'cancelled') {
            return $this->fail('Only cancelled orders can be deleted. Cancel the order first.');
        }

        $number = $order->order_number;
        $order->delete(); // items + history cascade; payments keep their record (order_id set to null)
        Log::info('Admin: order deleted', ['order_number' => $number]);

        return ['success' => true, 'message' => "Order {$number} deleted."];
    }

    // ── internals ───────────────────────────────────────────────

    /** Reverse exactly what OrderService::placeOrder took: variant stock or product qty, plus the coupon use. */
    private function restoreStock(Order $order): void
    {
        if ($order->stock_restored) {
            return;
        }

        foreach ($order->items as $item) {
            if ($item->product_attribute_id) {
                ProductAttribute::where('id', $item->product_attribute_id)->increment('stock', $item->qty);
            } elseif ($item->product_id) {
                ProductModel::where('id', $item->product_id)->increment('qty', $item->qty);
            }
        }

        if ($order->coupon_code) {
            CouponModel::where('coupon_code', $order->coupon_code)->where('used_count', '>', 0)->decrement('used_count');
        }

        $order->stock_restored = true;
    }

    /** Status-update email (includes tracking when present). Best-effort. */
    private function notifyCustomer(Order $order, ?int $adminId): bool
    {
        try {
            Mail::to($order->email)->send(new OrderStatusUpdatedMail($order));
        } catch (Throwable $e) {
            Log::error('Admin: order status email failed: ' . $e->getMessage(), ['order_id' => $order->id]);

            return false;
        }

        $this->log($order, 'email', null, $order->order_status, 'Status update email sent to customer.', $adminId, true);

        return true;
    }

    private function log(Order $order, string $type, ?string $from, ?string $to, ?string $note, ?int $adminId, bool $notified = false): void
    {
        OrderHistory::create([
            'order_id'          => $order->id,
            'type'              => $type,
            'from_value'        => $from,
            'to_value'          => $to,
            'note'              => $note ?: null,
            'admin_id'          => $adminId,
            'customer_notified' => $notified,
        ]);
    }

    private function fail(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }
}
