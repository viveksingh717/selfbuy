<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(OtpService $otpService)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException|\Exception $e) {
            Log::error('Google sign-in failed: '.$e->getMessage());

            return redirect()->route('home')->with('error', 'Google sign-in failed. Please try again.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name'        => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'email'       => $googleUser->getEmail(),
                'password'    => Hash::make(Str::random(32)),
                'role_type'   => 0,
                'status'      => 1,
                'is_verified' => 1,
                'terms'       => 1,
            ]);

            Log::info('Google sign-in: new account created', ['user_id' => $user->id, 'email' => $user->email]);
        } elseif ($user->status !== 1) {
            Log::warning('Google sign-in: account deactivated', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->route('home')->with('error', 'Your account has been deactivated. Please contact support.');
        } else {
            Log::info('Google sign-in: existing account matched', ['user_id' => $user->id, 'email' => $user->email]);
        }

        $otpResult = $otpService->generate($user, 'login');

        Log::info('Google sign-in: OTP requested', ['user_id' => $user->id, 'email' => $user->email]);

        session()->put([
            '2fa_user_id' => $user->id,
            '2fa_purpose' => 'login',
            '2fa_remember' => true,
        ]);

        return redirect()->route('home')->with(['open_auth_modal' => 'otp', 'success' => $otpResult['message']]);
    }
}
