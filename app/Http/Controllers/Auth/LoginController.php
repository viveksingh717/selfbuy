<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private OtpService $otpService,
    ) {
    }

    public function store(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator)->withInput($request->only('email'));
        }

        $result = $this->authService->verifyCredentials($request->email, $request->password);

        if (! $result['success']) {
            return $request->ajax()
                ? $rs->setErrorResponse($result['message'])
                : back()->with('error', $result['message'])->withInput($request->only('email'));
        }

        $user = $result['data'];

        $otpResult = $this->otpService->generate($user, 'login');

        Log::info('Login: OTP requested', ['user_id' => $user->id, 'email' => $user->email]);

        $request->session()->put([
            '2fa_user_id' => $user->id,
            '2fa_purpose' => 'login',
            '2fa_remember' => $request->boolean('remember_me'),
        ]);

        return $request->ajax()
            ? $rs->setSuccessResponse($otpResult['message'], ['step' => 'otp'])
            : redirect()->route('home')->with(['open_auth_modal' => 'otp', 'success' => $otpResult['message']]);
    }

    public function destroy(Request $request)
    {
        $userId = Auth::guard('web')->id();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout', ['user_id' => $userId]);

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
