<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use App\Services\OtpService;
use App\Services\ResponseService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function verify(Request $request, ResponseService $rs, CartService $cartService, WishlistService $wishlistService)
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

        $request->session()->forget(['2fa_user_id', '2fa_purpose', '2fa_remember']);

        Log::info('OTP: session established', ['user_id' => $user->id, 'purpose' => $purpose]);

        $message = $purpose === 'registration'
            ? 'Welcome, '.$user->name.'! Your account is verified.'
            : 'Logged in successfully!';

        return $request->ajax()
            ? $rs->setSuccessResponse($message, [])
            : redirect()->route('home')->with('success', $message);
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
