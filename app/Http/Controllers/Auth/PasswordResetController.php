<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator);
        }

        $status = Password::sendResetLink($request->only('email'));

        // Logged internally (unlike the user-facing message, which stays generic
        // regardless of outcome so the response can't be used to enumerate emails).
        Log::info('Password reset: link requested', ['email' => $request->email, 'status' => $status]);

        $message = 'If an account exists for that email, a password reset link has been sent.';

        return $request->ajax()
            ? $rs->setSuccessResponse($message, [])
            : back()->with('success', $message);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->only('email', 'token'));
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            Log::warning('Password reset: failed', ['email' => $request->email, 'status' => $status]);

            return back()->withErrors(['email' => __($status)])->withInput($request->only('email', 'token'));
        }

        Log::info('Password reset: completed', ['email' => $request->email]);

        return redirect()->route('home')->with([
            'open_auth_modal' => 'signin',
            'success' => 'Your password has been reset. Please sign in.',
        ]);
    }
}
