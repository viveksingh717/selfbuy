<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Shared by both the immediate (COD) and deferred (gateway payment) checkout
 * paths, since account creation can happen either right after the order is
 * placed or minutes later once a payment gateway confirms success.
 */
class CheckoutAccountService
{
    public function __construct(
        private AuthService $authService,
        private OtpService $otpService,
    ) {
    }

    public function maybeCreateAccount(Order $order, array $billingData): void
    {
        if ($order->user_id || !isset($billingData['account_password_encrypted'])) {
            return;
        }

        if (User::where('email', $billingData['email'])->exists()) {
            Log::info('Checkout: create-account skipped, email already registered', ['email' => $billingData['email']]);

            return;
        }

        try {
            $password = Crypt::decryptString($billingData['account_password_encrypted']);
        } catch (\Throwable $e) {
            Log::error('Checkout: could not decrypt stored account password: '.$e->getMessage(), ['order_id' => $order->id]);

            return;
        }

        $result = $this->authService->registerCustomer([
            'name' => trim($billingData['first_name'].' '.$billingData['last_name']),
            'email' => $billingData['email'],
            'phone_number' => $billingData['phone'],
            'password' => $password,
        ]);

        if (! $result['success']) {
            Log::warning('Checkout: create-account failed', ['email' => $billingData['email'], 'message' => $result['message']]);

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

        // One-shot: only opens the modal on the very next page load, unlike the
        // 2fa_* keys above which must persist across however many attempts/resends
        // the user needs to verify.
        session()->flash('open_auth_modal', 'otp');

        Log::info('Checkout: account created, linked to order', ['user_id' => $user->id, 'order_id' => $order->id]);
    }
}
