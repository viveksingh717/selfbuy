<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeOfferMail;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\OfferCouponService;
use App\Services\OtpService;
use App\Services\ResponseService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function verify(Request $request, ResponseService $rs, CartService $cartService, WishlistService $wishlistService, OrderService $orderService)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator);
        }

        [$user, $purpose, $error] = $this->resolvePending($request);

        if ($error) {
            return $request->ajax() ? $rs->setErrorResponse($error) : back()->with('error', $error);
        }

        $result = $this->otpService->verify($user, $purpose, $request->otp);

        if (! $result['success']) {
            return $request->ajax()
                ? $rs->setErrorResponse($result['message'])
                : back()->with('error', $result['message']);
        }

        if (! $user->is_verified) {
            $user->update(['is_verified' => 1]);
        }

        $preAuthSessionId = $request->session()->getId();
        $remember = (bool) $request->session()->get('2fa_remember', false);

        Auth::guard('web')->login($user, $remember);

        $cartService->mergeGuestCartIntoUser($preAuthSessionId, $user->id);
        $wishlistService->mergeGuestWishlistIntoUser($preAuthSessionId, $user->id);
        $orderService->linkGuestOrdersToUser($user->email, $user->id);

        // First-time sign-up: email registration, or a Google account created in this flow.
        $isNewAccount = $purpose === 'registration'
            || (int) $request->session()->get('2fa_new_account') === $user->id;

        $request->session()->forget(['2fa_user_id', '2fa_purpose', '2fa_remember', '2fa_new_account']);

        Log::info('OTP: session established', ['user_id' => $user->id, 'purpose' => $purpose]);

        $message = $isNewAccount
            ? 'Welcome, '.$user->name.'! Your account is verified.'
            : 'Logged in successfully!';

        $data = [];
        if ($isNewAccount && ($coupon = $this->grantWelcomeOffer($user))) {
            // The auth modal shows this in a popup that waits for the customer (see navbar.blade.php).
            $data['welcome_coupon'] = [
                'code'    => $coupon->coupon_code,
                'percent' => OfferCouponService::percentText($coupon->discount_value),
            ];
            $message .= ' Your welcome code for '.$data['welcome_coupon']['percent'].'% off is '.$coupon->coupon_code.'.';
        }

        $response = $request->ajax()
            ? $rs->setSuccessResponse($message, $data)
            : redirect()->route('home')->with('success', $message);

        // Remember that this browser belongs to a customer, so the "Sign Up & Get X% Off"
        // banner stays hidden even after they log out (~400 days, the browser maximum).
        return $response->withCookie(cookie()->forever('sb_has_account', '1'));
    }

    /** Issue + email the welcome coupon. Best-effort: never blocks the login. */
    private function grantWelcomeOffer(User $user)
    {
        try {
            $alreadyHad = (bool) $user->welcome_coupon_id;
            $coupon = app(OfferCouponService::class)->issueWelcomeFor($user);

            if ($coupon && !$alreadyHad) {
                try {
                    Mail::to($user->email)->send(new WelcomeOfferMail($user, $coupon));
                } catch (Throwable $e) {
                    Log::error('Welcome offer mail failed: '.$e->getMessage(), ['user_id' => $user->id]);
                }
            }

            return $coupon;
        } catch (Throwable $e) {
            Log::error('Welcome offer coupon failed: '.$e->getMessage(), ['user_id' => $user->id]);

            return null;
        }
    }

    public function resend(Request $request, ResponseService $rs)
    {
        [$user, $purpose, $error] = $this->resolvePending($request);

        if ($error) {
            return $request->ajax() ? $rs->setErrorResponse($error) : back()->with('error', $error);
        }

        $result = $this->otpService->resend($user, $purpose);

        if ($request->ajax()) {
            return $result['success']
                ? $rs->setSuccessResponse($result['message'], [])
                : $rs->setErrorResponse($result['message']);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function resolvePending(Request $request): array
    {
        $userId = $request->session()->get('2fa_user_id');
        $purpose = $request->session()->get('2fa_purpose');

        if (! $userId || ! $purpose) {
            Log::warning('OTP: no pending 2FA session found for request');

            return [null, null, 'Your verification session has expired. Please try again.'];
        }

        $user = User::find($userId);

        if (! $user) {
            Log::warning('OTP: pending 2FA session referenced a missing user', ['user_id' => $userId]);

            return [null, null, 'Your verification session has expired. Please try again.'];
        }

        return [$user, $purpose, null];
    }
}
