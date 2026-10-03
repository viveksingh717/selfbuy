<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CartService;
use App\Services\CheckoutAccountService;
use App\Services\OrderService;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'payment_method' => 'required|string|in:cod,razorpay,stripe,paypal,instamojo',
            'create_account' => 'nullable|boolean',
            'account_password' => array_filter([
                'nullable',
                'string',
                'min:8',
                $isGuest ? 'required_if:create_account,1' : null,
            ]),
        ]);

        // A new account can't reuse an email or phone number that is already registered.
        $validator->after(function ($validator) use ($request, $isGuest) {
            if (! $isGuest || ! $request->boolean('create_account')) {
                return;
            }
            if (User::where('email', $request->input('email'))->exists()) {
                $validator->errors()->add('email', 'An account with this email already exists. Please sign in, or untick "Create an account".');
            }
            $phone = User::normalizePhone($request->input('phone'));
            if ($phone && User::where('phone_number', $phone)->exists()) {
                $validator->errors()->add('phone', 'An account with this phone number already exists. Please sign in, or untick "Create an account".');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('account_password'));
        }

        $data = $validator->validated();
        $wantsAccount = $isGuest && $request->boolean('create_account');

        $billingData = collect($data)->except(['create_account', 'account_password', 'payment_method'])->toArray();

        if ($wantsAccount) {
            // One-way hash (same as users.password) - this may sit in the payments table while the
            // customer is on a gateway's checkout page, so it must never be recoverable.
            $billingData['account_password_hash'] = Hash::make($data['account_password']);
        }

        if (in_array($data['payment_method'], ['razorpay', 'stripe', 'paypal', 'instamojo'], true)) {
            return $this->startGatewayPayment($data['payment_method'], $billingData, $request);
        }

        $result = $this->orderService->placeOrder($billingData, 'cod', 'pending');

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput($request->except('account_password'));
        }

        $order = $result['data'];

        $this->checkoutAccountService->maybeCreateAccount($order, $billingData);

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(Request $request, string $orderNumber)
    {
        [$order, $denied] = $this->orderForViewer($request, $orderNumber);
        if ($denied) {
            return $denied;
        }

        return view('shop.order_success', compact('order'));
    }

    /** Customer's invoice / bill as a PDF - same access rule as the order page. */
    public function invoice(Request $request, string $orderNumber)
    {
        [$order, $denied] = $this->orderForViewer($request, $orderNumber);
        if ($denied) {
            return $denied;
        }

        return Pdf::loadView('admin.orders.invoice', compact('order'))
            ->setOption('isFontSubsettingEnabled', true) // embed only the glyphs used -> small PDF
            ->setPaper('a4')
            ->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * [order, null] when the visitor may see this order, or [null, redirect] when not.
     * Allowed: whoever placed it (same rule as the payment pages' ownsPayment()), or anyone
     * holding a signed link from that order's emails (Order::viewUrl() / invoiceUrl()).
     */
    private function orderForViewer(Request $request, string $orderNumber): array
    {
        // Not OrderService::findByOrderNumber(): that one only returns the order to its owner,
        // which would hide it from the signed email links. Access is decided just below.
        $order = Order::with('items')->where('order_number', $orderNumber)->first();

        if (!$order) {
            return [null, redirect()->route('home')];
        }

        if (!$this->ownsOrder($order) && !$request->hasValidSignature()) {
            return [null, Auth::guard('web')->check()
                ? redirect()->route('myaccount')->with('error', "Order #{$order->order_number} belongs to a different account. Log in with the account that placed it, or use the \"View your order\" link in its email.")
                : redirect()->route('home')->with(['open_auth_modal' => 'signin', 'error' => 'Please sign in to view your order.'])];
        }

        return [$order, null];
    }

    private function startGatewayPayment(string $gateway, array $billingData, Request $request)
    {
        $result = $this->paymentService->initiate($gateway, $billingData);

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput($request->except('account_password'));
        }

        return redirect()->route("payment.{$gateway}.show", $result['data']->id);
    }

    private function ownsOrder($order): bool
    {
        return Auth::guard('web')->check()
            ? (int) $order->user_id === (int) Auth::guard('web')->id()
            : $order->session_id !== null && $order->session_id === Session::getId();
    }
}
