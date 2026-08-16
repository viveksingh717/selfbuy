<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutAccountService;
use App\Services\OrderService;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private PaymentService $paymentService,
        private CheckoutAccountService $checkoutAccountService,
    ) {
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty. Add some products before checking out.');
        }

        $totals = $this->cartService->getTotals();
        $appliedCoupon = $this->cartService->getAppliedCoupon();
        $user = Auth::guard('web')->user();

        // The user profile only has name/email/phone — no address fields at all —
        // so the full shipping address (city, state, postal code, country) can only
        // come from a previous order, not the account itself.
        $lastOrder = $user ? Order::where('user_id', $user->id)->latest()->first() : null;

        return view('shop.checkout', compact('cartItems', 'totals', 'appliedCoupon', 'user', 'lastOrder'));
    }

    public function store(Request $request)
    {
        $isGuest = ! Auth::guard('web')->check();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email:rfc,filter|max:150',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\- ]{3,20}$/'],
            'country' => 'required|string|max:100',
            'order_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cod,razorpay',
            'create_account' => 'nullable|boolean',
            'account_password' => array_filter([
                'nullable',
                'string',
                'min:6',
                $isGuest ? 'required_if:create_account,1' : null,
            ]),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('account_password'));
        }

        $data = $validator->validated();
        $wantsAccount = $isGuest && $request->boolean('create_account');

        $billingData = collect($data)->except(['create_account', 'account_password', 'payment_method'])->toArray();

        if ($wantsAccount) {
            // Encrypted, never plaintext — this may sit in the payments table for
            // minutes if the customer takes a while on a gateway's checkout page.
            $billingData['account_password_encrypted'] = Crypt::encryptString($data['account_password']);
        }

        if ($data['payment_method'] === 'razorpay') {
            return $this->startGatewayPayment('razorpay', $billingData, $request);
        }

        $result = $this->orderService->placeOrder($billingData, 'cod', 'pending');

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput($request->except('account_password'));
        }

        $order = $result['data'];

        $this->checkoutAccountService->maybeCreateAccount($order, $billingData);

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber)
    {
        $order = $this->orderService->findByOrderNumber($orderNumber);

        if (!$order) {
            return redirect()->route('home');
        }

        return view('shop.order_success', compact('order'));
    }

    private function startGatewayPayment(string $gateway, array $billingData, Request $request)
    {
        $result = $this->paymentService->initiate($gateway, $billingData);

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput($request->except('account_password'));
        }

        return redirect()->route("payment.{$gateway}.show", $result['data']->id);
    }
}
