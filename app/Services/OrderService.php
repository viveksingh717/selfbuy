<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CouponModel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OrderService
{
    private $cartService;

    public function __construct(
        CartService $cartService,
        private OrderNotificationService $orderNotificationService,
    ) {
        $this->cartService = $cartService;
    }

    public function placeOrder(
        array $customerData,
        string $paymentMethod = 'cod',
        string $paymentStatus = 'pending',
        ?float $expectedTotal = null,
    ): array {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return ['status' => false, 'message' => 'Your cart is empty'];
        }

        $items = $cartItems->map(fn ($item) => $this->normalizeCartItem($item))->all();
        $totals = $this->cartService->getTotals();

        // For gateway payments the amount was already charged (fixed at the
        // gateway order's creation) before this runs. If the cart changed in
        // between, the charge and the order total would mismatch — refuse
        // rather than silently create an order for the wrong amount.
        if ($expectedTotal !== null && abs($totals['cart_total'] - $expectedTotal) > 0.01) {
            return ['status' => false, 'message' => 'Your cart changed after payment was completed. Please contact support with your payment reference.'];
        }

        return $this->createOrder(
            $customerData,
            $items,
            $totals,
            Auth::guard('web')->id(),
            Session::getId(),
            $paymentMethod,
            $paymentStatus,
            fn () => $this->cartService->clearCart(),
        );
    }

    /**
     * Used when the order is created after the fact from a stored snapshot of
     * the cart taken when a gateway payment was initiated — the request that
     * completes the payment (a webhook, in particular) has no session or live
     * cart of its own to read.
     */
    public function placeOrderFromSnapshot(
        array $customerData,
        array $items,
        array $totals,
        ?int $userId,
        ?string $sessionId,
        string $paymentMethod,
        string $paymentStatus,
    ): array {
        if (empty($items)) {
            return ['status' => false, 'message' => 'No items to order'];
        }

        return $this->createOrder(
            $customerData,
            $items,
            $totals,
            $userId,
            $sessionId,
            $paymentMethod,
            $paymentStatus,
            function () use ($userId, $sessionId) {
                $query = Cart::query();
                $userId ? $query->where('user_id', $userId) : $query->where('session_id', $sessionId);
                $query->delete();

                // Reaches the real browser session for the common case (the client-side
                // verify callback, same tab that started checkout). A no-op for a webhook
                // request, which has no session of its own to clear — harmless either way.
                Session::forget(['cart_coupon_code', 'cart_shipping_method']);
            },
        );
    }

    public function normalizeCartItem($item): array
    {
        return [
            'product_id' => $item->product_id,
            'product_attribute_id' => $item->product_attribute_id,
            'product_name' => $item->product->product_name ?? 'Unavailable product',
            'variant_label' => trim(implode(' ', array_filter([
                optional($item->color)->color_name,
                optional($item->size)->size_name,
            ]))) ?: null,
            'qty' => $item->qty,
            'unit_price' => $item->unit_price,
            'extra_price' => $item->extra_price,
            'line_total' => $item->line_total,
        ];
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return null;
        }

        $isOwner = Auth::guard('web')->check()
            ? $order->user_id === Auth::guard('web')->id()
            : $order->session_id === Session::getId();

        return $isOwner ? $order : null;
    }

    private function createOrder(
        array $customerData,
        array $items,
        array $totals,
        ?int $userId,
        ?string $sessionId,
        string $paymentMethod,
        string $paymentStatus,
        Closure $clearCart,
    ): array {
        try {
            $order = DB::transaction(function () use ($items, $totals, $customerData, $userId, $sessionId, $paymentMethod, $paymentStatus, $clearCart) {
                // Lock and re-validate stock for every line before committing to the order.
                foreach ($items as $item) {
                    if (!empty($item['product_attribute_id'])) {
                        $attribute = ProductAttribute::where('id', $item['product_attribute_id'])->lockForUpdate()->first();

                        if (!$attribute || $attribute->stock < $item['qty']) {
                            throw new Exception("\"{$item['product_name']}\" no longer has enough stock");
                        }
                    } else {
                        $product = ProductModel::where('id', $item['product_id'])->lockForUpdate()->first();

                        if (!$product || $product->qty < $item['qty']) {
                            throw new Exception("\"{$item['product_name']}\" no longer has enough stock");
                        }
                    }
                }

                $order = Order::create(array_merge($customerData, [
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'subtotal' => $totals['subtotal'],
                    'discount' => $totals['discount'],
                    'coupon_code' => $totals['coupon_code'],
                    'shipping_cost' => $totals['shipping'],
                    'shipping_method' => $totals['shipping_method'],
                    'total' => $totals['cart_total'],
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'order_status' => 'pending',
                ]));

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_attribute_id' => $item['product_attribute_id'],
                        'product_name' => $item['product_name'],
                        'variant_label' => $item['variant_label'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'extra_price' => $item['extra_price'],
                        'line_total' => $item['line_total'],
                    ]);

                    if (!empty($item['product_attribute_id'])) {
                        ProductAttribute::where('id', $item['product_attribute_id'])->decrement('stock', $item['qty']);
                    } else {
                        ProductModel::where('id', $item['product_id'])->decrement('qty', $item['qty']);
                    }
                }

                if (!empty($totals['coupon_code'])) {
                    CouponModel::where('coupon_code', $totals['coupon_code'])->increment('used_count');
                }

                $clearCart();

                return $order;
            });

            // Outside the transaction — a slow/failed send must never roll back a
            // real order, and every payment method (COD, Razorpay, future gateways)
            // funnels through here, so this is the single place this needs wiring.
            $this->orderNotificationService->sendOrderConfirmation($order);

            return ['status' => true, 'message' => 'Order placed successfully', 'data' => $order];

        } catch (Exception $e) {
            Log::error('Order Placement Error: ' . $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD' . now()->format('ymd') . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
