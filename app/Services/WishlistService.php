<?php

namespace App\Services;

use App\Models\ProductAttribute;
use App\Models\ProductModel;
use App\Models\Wishlist;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WishlistService
{
    /**
     * Criteria identifying the current wishlist owner (logged-in user or guest session).
     */
    private function ownerCriteria()
    {
        if (Auth::guard('web')->check()) {
            return ['user_id' => Auth::guard('web')->id()];
        }

        return ['session_id' => Session::getId()];
    }

    /**
     * Adds the product if it isn't already wishlisted, removes it if it is —
     * matching a single heart-icon click, not separate add/remove controls.
     * Unlike cart, a variant is never required: wishlisting is a bookmark, not
     * a purchase, so "I'll pick a size later" is valid — but if a variant *is*
     * given, it still has to genuinely belong to the product.
     */
    public function toggle(int $productId, ?int $productAttributeId = null): array
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
            }

            $criteria = array_merge($this->ownerCriteria(), [
                'product_id' => $product->id,
                'product_attribute_id' => $attribute->id ?? null,
            ]);

            $existing = Wishlist::where($criteria)->first();

            if ($existing) {
                $existing->delete();

                return [
                    'status' => true,
                    'action' => 'removed',
                    'message' => 'Removed from wishlist',
                    'wishlist_count' => $this->getWishlistCount(),
                ];
            }

            $wishlistItem = Wishlist::create(array_merge($criteria, [
                'color_id' => $attribute->color_id ?? null,
                'size_id' => $attribute->size_id ?? null,
            ]));

            return [
                'status' => true,
                'action' => 'added',
                'message' => 'Added to wishlist',
                'data' => $wishlistItem,
                'wishlist_count' => $this->getWishlistCount(),
            ];

        } catch (Exception $e) {
            Log::error('Wishlist Toggle Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to update wishlist'];
        }
    }

    public function removeItem(int $wishlistId): array
    {
        try {
            $item = Wishlist::where('id', $wishlistId)->where($this->ownerCriteria())->first();

            if (empty($item)) {
                return ['status' => false, 'message' => 'Wishlist item not found'];
            }

            $item->delete();

            return [
                'status' => true,
                'message' => 'Removed from wishlist',
                'wishlist_count' => $this->getWishlistCount(),
            ];

        } catch (Exception $e) {
            Log::error('Wishlist Remove Error: ' . $e->getMessage());

            return ['status' => false, 'message' => 'Failed to remove item'];
        }
    }

    public function getWishlistItems()
    {
        return Wishlist::with(['product', 'productAttribute', 'color', 'size'])
            ->where($this->ownerCriteria())
            ->latest()
            ->get();
    }

    public function getWishlistCount(): int
    {
        return Wishlist::where($this->ownerCriteria())->count();
    }

    /**
     * Product ids currently wishlisted by the owner, ignoring variant — used to
     * pre-render a filled heart icon on listing/detail pages for items already saved.
     */
    public function getWishlistedProductIds(): array
    {
        return Wishlist::where($this->ownerCriteria())->pluck('product_id')->unique()->values()->all();
    }

    public function mergeGuestWishlistIntoUser(string $sessionId, int $userId): void
    {
        $guestItems = Wishlist::where('session_id', $sessionId)->whereNull('user_id')->get();

        foreach ($guestItems as $guestItem) {
            $alreadyWishlisted = Wishlist::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->where('product_attribute_id', $guestItem->product_attribute_id)
                ->exists();

            if ($alreadyWishlisted) {
                $guestItem->delete();
            } else {
                $guestItem->user_id = $userId;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
    }
}
