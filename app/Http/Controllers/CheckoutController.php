<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private AuthService $authService,
        private OtpService $otpService,
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

        return view('shop.checkout', compact('cartItems', 'totals', 'appliedCoupon', 'user'));
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
        $orderData = collect($data)->except(['create_account', 'account_password'])->toArray();

        $result = $this->orderService->placeOrder($orderData);

        if (!$result['status']) {
            return back()->with('error', $result['message'])->withInput($request->except('account_password'));
        }

        $order = $result['data'];

        if ($wantsAccount) {
            $this->createAccountForGuest($order, $data);
        }

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

    /**
     * A guest can opt to create an account while checking out. The order already
     * succeeded by this point, so account creation failing is never allowed to
     * fail the order itself — it's a best-effort add-on, logged either way.
     */
    private function createAccountForGuest($order, array $data): void
    {
        if (User::where('email', $data['email'])->exists()) {
            Log::info('Checkout: create-account skipped, email already registered', ['email' => $data['email']]);

            return;
        }

        $result = $this->authService->registerCustomer([
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'phone_number' => $data['phone'],
            'password' => $data['account_password'],
        ]);

        if (! $result['success']) {
            Log::warning('Checkout: create-account failed', ['email' => $data['email'], 'message' => $result['message']]);

            return;
        }

        $user = $result['data'];
        $order->update(['user_id' => $user->id]);

        $this->otpService->generate($user, 'registration');

        session()->put([
            '2fa_user_id' => $user->id,
            '2fa_purpose' => 'registration',
            '2fa_remember' => false,
        ]);

        // One-shot: only opens the modal on the very next page load (the order
        // success page), unlike the 2fa_* keys above which must persist across
        // however many attempts/resends the user needs to verify.
        session()->flash('open_auth_modal', 'otp');

        Log::info('Checkout: account created for guest, linked to order', ['user_id' => $user->id, 'order_id' => $order->id]);
    }
}
