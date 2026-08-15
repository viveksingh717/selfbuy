<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();
        $subtotal = (float) $cartItems->sum(fn ($item) => $item->line_total);

        return view('shop.cart', compact('cartItems', 'subtotal'));
    }

    public function store(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:product_models,id',
            'product_attribute_id' => 'nullable|integer|exists:product_attributes,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator)->withInput();
        }

        $result = $this->cartService->addItem(
            (int) $request->product_id,
            $request->product_attribute_id ? (int) $request->product_attribute_id : null,
            (int) $request->input('qty', 1)
        );

        if ($request->ajax()) {
            if (!$result['status']) {
                return $rs->setErrorResponse($result['message']);
            }

            return $rs->setSuccessResponse($result['message'], [
                'cart_count' => $result['cart_count'],
                'cart_dropdown_html' => $this->renderCartDropdown(),
            ]);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function update(Request $request, $id, ResponseService $rs)
    {
        $result = $this->cartService->updateQuantity((int) $id, (int) $request->input('qty', 1));

        if ($request->ajax()) {
            if (!$result['status']) {
                return $rs->setErrorResponse($result['message']);
            }

            $payload = $this->totalsPayload($result);
            $payload['line_total'] = $result['data']->line_total;

            return $rs->setSuccessResponse($result['message'], $payload);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function destroy(Request $request, $id, ResponseService $rs)
    {
        $result = $this->cartService->removeItem((int) $id);

        if ($request->ajax()) {
            return $result['status']
                ? $rs->setSuccessResponse($result['message'], $this->totalsPayload($result))
                : $rs->setErrorResponse($result['message']);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function applyCoupon(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator);
        }

        $result = $this->cartService->applyCoupon($request->coupon_code);

        if ($request->ajax()) {
            return $result['status']
                ? $rs->setSuccessResponse($result['message'], $this->totalsPayload($result))
                : $rs->setErrorResponse($result['message']);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(Request $request, ResponseService $rs)
    {
        $result = $this->cartService->removeCoupon();

        if ($request->ajax()) {
            return $rs->setSuccessResponse($result['message'], $this->totalsPayload($result));
        }

        return back()->with('success', $result['message']);
    }

    public function setShipping(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:free,standard,express',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator);
        }

        $result = $this->cartService->setShippingMethod($request->method);

        if ($request->ajax()) {
            return $result['status']
                ? $rs->setSuccessResponse($result['message'], $this->totalsPayload($result))
                : $rs->setErrorResponse($result['message']);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    private function totalsPayload(array $result): array
    {
        return collect($result)->except(['status', 'message', 'data'])->toArray();
    }

    private function renderCartDropdown(): string
    {
        $cartItems = $this->cartService->getCartItems();

        return view('layouts.inc.cart_dropdown', [
            'headerCartItems' => $cartItems->take(5),
            'headerCartTotal' => (float) $cartItems->sum(fn ($item) => $item->line_total),
        ])->render();
    }
}
