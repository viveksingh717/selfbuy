<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CouponModel;
use App\Models\ProductAttribute;
use App\Models\ProductModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SHIPPING_RATES = [
        'free' => 0.0,
        'standard' => 10.0,
        'express' => 20.0,
    ];

    /**
     * Criteria identifying the current cart owner (logged-in user or guest session).
     */
    private function ownerCriteria()
    {
        if (Auth::guard('web')->check()) {
            return ['user_id' => Auth::guard('web')->id()];
        }

        return ['session_id' => Session::getId()];
    }

    public function addItem(int $productId, ?int $productAttributeId, int $qty): array
    {
        try {
            $product = ProductModel::find($productId);

            if (empty($product) || $product->status != 1) {
                return ['status' => false, 'message' => 'Product not found'];
            }

            $attribute = null;

            if ($productAttributeId) {
                $attribute = ProductAttribute::where('id', $productAttributeId)
                    ->where('product_id', $product->id)
                    ->first();

                if (empty($attribute)) {
                    return ['status' => false, 'message' => 'Selected variant not found'];
                }
            } elseif ($product->attributes()->exists()) {
                return ['status' => false, 'message' => 'Please select a color/size before adding to cart'];
            }

            $stock = $attribute ? $attribute->stock : $product->qty;

            if ($product->stock_status !== 'in_stock' || $stock < 1) {
                return ['status' => false, 'message' => 'Product is out of stock'];
            }

            $cartItem = Cart::firstOrNew(array_merge($this->ownerCriteria(), [
                'product_id' => $product->id,
                'product_attribute_id' => $attribute->id ?? null,
            ]));

            $requestedQty = ($cartItem->qty ?? 0) + $qty;

            if ($requestedQty > $stock) {
                $requestedQty = $stock;
            }

            $cartItem->qty = $requestedQty;
            $cartItem->unit_price = $product->selling_price;
            $cartItem->extra_price = $attribute->extra_price ?? 0;
            $cartItem->color_id = $attribute->color_id ?? null;
            $cartItem->size_id = $attribute->size_id ?? null;
            $cartItem->save();

            return [
                'status' => true,
                'message' => 'Product added to cart',
                'data' => $cartItem,
                'cart_count' => $this->getCartCount(),
            ];

        } catch (Exception $e) {
            Log::error('Cart Add Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to add product to cart'];
        }
    }

    public function updateQuantity(int $cartId, int $qty): array
    {
        try {
            $cartItem = Cart::where('id', $cartId)->where($this->ownerCriteria())->first();

            if (empty($cartItem)) {
                return ['status' => false, 'message' => 'Cart item not found'];
            }

            if ($qty < 1) {
                return $this->removeItem($cartId);
            }

            $stock = $cartItem->productAttribute ? $cartItem->productAttribute->stock : $cartItem->product->qty;

            if ($qty > $stock) {
                return ['status' => false, 'message' => "Only {$stock} item(s) available in stock"];
            }

            $cartItem->qty = $qty;
            $cartItem->save();

            return array_merge([
                'status' => true,
                'message' => 'Cart updated',
                'data' => $cartItem,
            ], $this->getTotals());

        } catch (Exception $e) {
            Log::error('Cart Update Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to update cart'];
        }
    }

    public function removeItem(int $cartId): array
    {
        try {
            $cartItem = Cart::where('id', $cartId)->where($this->ownerCriteria())->first();

            if (empty($cartItem)) {
                return ['status' => false, 'message' => 'Cart item not found'];
            }

            $cartItem->delete();

            return array_merge([
                'status' => true,
                'message' => 'Product removed from cart',
            ], $this->getTotals());

        } catch (Exception $e) {
            Log::error('Cart Remove Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to remove product from cart'];
        }
    }

    public function getCartItems()
    {
        return Cart::with(['product', 'productAttribute', 'color', 'size'])
            ->where($this->ownerCriteria())
            ->latest()
            ->get();
    }

    public function getCartCount(): int
    {
        return (int) Cart::where($this->ownerCriteria())->sum('qty');
    }

    /**
     * Empty the cart and clear coupon/shipping selection, called after an order is placed.
     */
    public function clearCart(): void
    {
        Cart::where($this->ownerCriteria())->delete();
        Session::forget(['cart_coupon_code', 'cart_shipping_method']);
    }

    /**
     * Subtotal, discount, shipping and grand total in one pass (avoids repeated cart queries).
     */
    public function getTotals(): array
    {
        $cartItems = $this->getCartItems();
        $subtotal = (float) $cartItems->sum(fn ($item) => $item->line_total);
        $coupon = $this->resolveValidCoupon($cartItems, $subtotal);
        $discount = $coupon ? $this->calculateDiscount($coupon, $cartItems, $subtotal) : 0.0;
        $shippingMethod = Session::get('cart_shipping_method', 'free');
        $shipping = ($coupon && $coupon->discount_type === 'free_shipping')
            ? 0.0
            : (self::SHIPPING_RATES[$shippingMethod] ?? 0.0);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'shipping_method' => $shippingMethod,
            'coupon_code' => $coupon->coupon_code ?? null,
            'cart_count' => (int) $cartItems->sum('qty'),
            'cart_total' => max(0.0, $subtotal - $discount + $shipping),
        ];
    }

    public function getAppliedCoupon(): ?CouponModel
    {
        $cartItems = $this->getCartItems();
        $subtotal = (float) $cartItems->sum(fn ($item) => $item->line_total);

        return $this->resolveValidCoupon($cartItems, $subtotal);
    }

    public function applyCoupon(string $code): array
    {
        try {
            $coupon = CouponModel::where('coupon_code', strtoupper(trim($code)))->first();

            if (!$coupon) {
                return ['status' => false, 'message' => 'Invalid coupon code'];
            }

            $cartItems = $this->getCartItems();
            $subtotal = (float) $cartItems->sum(fn ($item) => $item->line_total);
            $validation = $this->validateCoupon($coupon, $cartItems, $subtotal);

            if (!$validation['valid']) {
                return ['status' => false, 'message' => $validation['message']];
            }

            Session::put('cart_coupon_code', $coupon->coupon_code);

            return array_merge(['status' => true, 'message' => 'Coupon applied successfully'], $this->getTotals());

        } catch (Exception $e) {
            Log::error('Cart Coupon Apply Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to apply coupon'];
        }
    }

    public function removeCoupon(): array
    {
        Session::forget('cart_coupon_code');

        return array_merge(['status' => true, 'message' => 'Coupon removed'], $this->getTotals());
    }

    public function setShippingMethod(string $method): array
    {
        if (!array_key_exists($method, self::SHIPPING_RATES)) {
            return ['status' => false, 'message' => 'Invalid shipping method'];
        }

        Session::put('cart_shipping_method', $method);

        return array_merge(['status' => true, 'message' => 'Shipping updated'], $this->getTotals());
    }

    private function resolveValidCoupon($cartItems, float $subtotal): ?CouponModel
    {
        $code = Session::get('cart_coupon_code');

        if (!$code) {
            return null;
        }

        $coupon = CouponModel::where('coupon_code', $code)->first();

        if (!$coupon || !$this->validateCoupon($coupon, $cartItems, $subtotal)['valid']) {
            Session::forget('cart_coupon_code');

            return null;
        }

        return $coupon;
    }

    private function validateCoupon(CouponModel $coupon, $cartItems, float $subtotal): array
    {
        if ($coupon->status != 1) {
            return ['valid' => false, 'message' => 'This coupon is no longer active'];
        }

        if ($coupon->expiry_date && $coupon->expiry_date->isPast()) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }

        if ($coupon->start_date && $coupon->start_date->isFuture()) {
            return ['valid' => false, 'message' => 'This coupon is not active yet'];
        }

        if (!is_null($coupon->usage_limit) && $coupon->used_count >= $coupon->usage_limit) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }

        if ($coupon->discount_type === 'buy_x_get_y') {
            return ['valid' => false, 'message' => 'This coupon type is not supported yet'];
        }

        if ($coupon->applicable_to === 'category') {
            return ['valid' => false, 'message' => 'This coupon is restricted to specific categories and cannot be applied yet'];
        }

        if ($coupon->applicable_to === 'product') {
            $eligible = $cartItems->contains(fn ($item) => optional($item->product)->coupon_id === $coupon->id);

            if (!$eligible) {
                return ['valid' => false, 'message' => 'This coupon does not apply to any item in your cart'];
            }
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            return ['valid' => false, 'message' => 'Minimum order amount of ₹' . number_format((float) $coupon->min_order_amount, 2) . ' required for this coupon'];
        }

        return ['valid' => true, 'message' => ''];
    }

    private function calculateDiscount(CouponModel $coupon, $cartItems, float $subtotal): float
    {
        if ($coupon->discount_type === 'free_shipping') {
            return 0.0;
        }

        $base = $subtotal;

        if ($coupon->applicable_to === 'product') {
            $base = (float) $cartItems
                ->filter(fn ($item) => optional($item->product)->coupon_id === $coupon->id)
                ->sum(fn ($item) => $item->line_total);
        }

        $discount = $coupon->discount_type === 'percentage'
            ? $base * ((float) $coupon->discount_value / 100)
            : (float) $coupon->discount_value;

        if ($coupon->max_discount_amount) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        return min($discount, $base);
    }

    /**
     * Merge a guest session's cart into the logged-in user's cart, called on login.
     */
    public function mergeGuestCartIntoUser(string $sessionId, int $userId): void
    {
        $guestItems = Cart::where('session_id', $sessionId)->whereNull('user_id')->get();

        foreach ($guestItems as $guestItem) {
            $userItem = Cart::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->where('product_attribute_id', $guestItem->product_attribute_id)
                ->first();

            if ($userItem) {
                $stock = $guestItem->productAttribute ? $guestItem->productAttribute->stock : $guestItem->product->qty;
                $userItem->qty = min($userItem->qty + $guestItem->qty, $stock);
                $userItem->save();
                $guestItem->delete();
            } else {
                $guestItem->user_id = $userId;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
    }
}
