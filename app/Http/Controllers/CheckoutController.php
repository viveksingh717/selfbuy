<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    private $cartService;
    private $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Add some products before checking out.');
        }

        $totals = $this->cartService->getTotals();
        $appliedCoupon = $this->cartService->getAppliedCoupon();

        return view('shop.checkout', compact('cartItems', 'totals', 'appliedCoupon'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'order_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->orderService->placeOrder($validator->validated());

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return redirect()->route('checkout.success', $result['data']->order_number);
    }

    public function success(string $orderNumber)
    {
        $order = $this->orderService->findByOrderNumber($orderNumber);

        if (!$order) {
            return redirect()->route('home');
        }

        return view('shop.order_success', compact('order'));
    }
}
