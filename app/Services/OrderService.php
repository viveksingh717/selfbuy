<?php

namespace App\Services;

use App\Models\CouponModel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OrderService
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function placeOrder(array $customerData): array
    {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return ['status' => false, 'message' => 'Your cart is empty'];
        }

        try {
            $order = DB::transaction(function () use ($cartItems, $customerData) {
                // Lock and re-validate stock for every line before committing to the order.
                foreach ($cartItems as $item) {
                    if ($item->product_attribute_id) {
                        $attribute = ProductAttribute::where('id', $item->product_attribute_id)->lockForUpdate()->first();

                        if (!$attribute || $attribute->stock < $item->qty) {
                            throw new Exception("\"{$item->product->product_name}\" no longer has enough stock");
                        }
                    } else {
                        $product = ProductModel::where('id', $item->product_id)->lockForUpdate()->first();

                        if (!$product || $product->qty < $item->qty) {
                            throw new Exception("\"{$item->product->product_name}\" no longer has enough stock");
                        }
                    }
                }

                $totals = $this->cartService->getTotals();
                $coupon = $this->cartService->getAppliedCoupon();

                $order = Order::create(array_merge($customerData, [
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => Auth::guard('web')->id(),
                    'session_id' => Session::getId(),
                    'subtotal' => $totals['subtotal'],
                    'discount' => $totals['discount'],
                    'coupon_code' => $totals['coupon_code'],
                    'shipping_cost' => $totals['shipping'],
                    'shipping_method' => $totals['shipping_method'],
                    'total' => $totals['cart_total'],
                    'payment_method' => 'cod',
                    'payment_status' => 'pending',
                    'order_status' => 'pending',
                ]));

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
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
                    ]);

                    if ($item->product_attribute_id) {
                        ProductAttribute::where('id', $item->product_attribute_id)->decrement('stock', $item->qty);
                    } else {
                        ProductModel::where('id', $item->product_id)->decrement('qty', $item->qty);
                    }
                }

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                $this->cartService->clearCart();

                return $order;
            });

            return ['status' => true, 'message' => 'Order placed successfully', 'data' => $order];

        } catch (Exception $e) {
            Log::error('Order Placement Error: ' . $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
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

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD' . now()->format('ymd') . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
