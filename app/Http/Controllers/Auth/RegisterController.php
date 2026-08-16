<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private OtpService $otpService,
    ) {
    }

    public function store(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:100',
            'email'        => 'required|email:rfc,filter|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'password'     => 'required|string|min:6',
            'terms'        => 'required|accepted',
        ], [
            'email.unique'   => 'An account with this email already exists. Please sign in instead.',
            'terms.required' => 'You must agree to the privacy policy.',
            'terms.accepted' => 'You must agree to the privacy policy.',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator)->withInput($request->except('password'));
        }

        $result = $this->authService->registerCustomer($validator->validated());

        if (! $result['success']) {
            return $request->ajax()
                ? $rs->setErrorResponse($result['message'])
                : back()->with('error', $result['message'])->withInput($request->except('password'));
        }

        $user = $result['data'];

        $otpResult = $this->otpService->generate($user, 'registration');

        Log::info('Register: OTP requested for new account', ['user_id' => $user->id, 'email' => $user->email]);

        $request->session()->put([
            '2fa_user_id' => $user->id,
            '2fa_purpose' => 'registration',
            '2fa_remember' => false,
        ]);

        return $request->ajax()
            ? $rs->setSuccessResponse($otpResult['message'], ['step' => 'otp'])
            : redirect()->route('home')->with(['open_auth_modal' => 'otp', 'success' => $otpResult['message']]);
    }
}
