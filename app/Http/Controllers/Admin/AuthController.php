<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct() {
        $this->authService = new AuthService;
    }

    public function login() {
        $rememberedEmail   = request()->cookie('admin_remember_email');
        $rememberedChecked = request()->cookie('admin_remember_checked');

        return view('admin.auth.login', compact('rememberedEmail', 'rememberedChecked'));
    }

    public function login_process(Request $request) {
        $request->validate([
            'email' => 'required|email:rfc,dns|regex:/(.+)@(.+)\.(.+)/i|max:255',
            'password' => [
                'required',
                'string',
                'min:6',             // must be at least 6 characters in length
                // 'regex:/[a-z]/',      // must contain at least one lowercase letter
                // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                // 'regex:/[0-9]/',      // must contain at least one digit
                // 'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            // 'password' => [
            //     'required',
            //     'string',
            //     Password::min(6)
            //         ->mixedCase()
            //         ->numbers()
            //         ->symbols()
            //         ->uncompromised()
            // ]
        ]);

        $email = $request->email;
        $password = $request->password;
        $remember_me = $request->boolean('remember_me');

        $authCheck = $this->authService->authCheck($email, $password, $remember_me);

        if ($authCheck['success'] === true) {

            $response = redirect()->route('admin.dashboard')->with('success', $authCheck['message']);

            // ── Remember email via cookie ─────────────────
            if ($remember_me) {
                // store for 30 days
                $response->withCookie(cookie('admin_remember_email', $email, 60 * 24 * 30));
                $response->withCookie(cookie('admin_remember_checked', '1', 60 * 24 * 30));
            } else {
                // clear cookies if unchecked
                $response->withCookie(cookie()->forget('admin_remember_email'));
                $response->withCookie(cookie()->forget('admin_remember_checked'));
            }

            return $response;

        } else {
            return redirect()->back()->with('error', $authCheck['message'])
                ->withInput($request->only(['email', 'remember_me']));
        }

        return redirect()->back()->with('error', 'Something went wrong, please check inputs and try again')
            ->withInput($request->only(['email','remember_me']));
    }

    public function register() {
        $this->ensureRegistrationEnabled();

        return view('admin.auth.register');
    }

    /** Admin sign-up is closed unless ADMIN_REGISTRATION_ENABLED=true (see config/auth.php). */
    private function ensureRegistrationEnabled(): void
    {
        abort_unless(config('auth.admin_registration'), 404);
    }

    public function register_process(Request $request)
    {
        $this->ensureRegistrationEnabled();

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email:rfc,dns|regex:/(.+)@(.+)\.(.+)/i|max:255|unique:users,email',
            'password' => ['required', 'string', 'min:6'],
            'terms'    => 'required|accepted',
        ], [
            'email.unique'    => 'An account with this email already exists. Please sign in instead.',
            'terms.required'  => 'You must agree to the terms and policy.',
            'terms.accepted'  => 'You must agree to the terms and policy.',
        ]);

        $data = $request->only(['name', 'email', 'password','terms']);

        $result = $this->authService->registerAdmin($data);

        if ($result['success'] === true) {
            return redirect()->route('admin.login')
                ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])
            ->withInput($request->only(['name', 'email']));
    }

    public function term_condition() {
        return view('admin.layouts.terms_condition');
    }

    public function forget_password() {
        return view('admin.auth.forget_form');
    }

    /**
     * Admins and customers share the same users table (role_type distinguishes
     * them) and the same password_reset_tokens table, so this reuses Laravel's
     * own password broker rather than a bespoke mechanism — see
     * User::sendPasswordResetNotification(), which branches on role_type to
     * point the emailed link at this admin flow instead of the storefront one.
     */
    public function sendResetLink(Request $request) {
        $request->validate([
            'email' => 'required|email:rfc,filter|max:255',
        ]);

        $email = $request->email;

        // Only ever actually send a link if this email belongs to an admin
        // account — the response message is identical either way, so this
        // can't be used to enumerate which emails exist or which are admins.
        $isAdmin = User::where('email', $email)->where('role_type', 1)->exists();

        if ($isAdmin) {
            $status = Password::sendResetLink(['email' => $email]);
            Log::info('Admin password reset: link requested', ['email' => $email, 'status' => $status]);
        } else {
            Log::info('Admin password reset: link requested for non-admin or unknown email', ['email' => $email]);
        }

        return redirect()->back()->with('success', 'Password reset link has been sent.');
    }

    public function showResetForm(Request $request, string $token) {
        return view('admin.auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset_password(Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email:rfc,filter',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->sendPasswordChangedNotification();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            Log::warning('Admin password reset: failed', ['email' => $request->email, 'status' => $status]);

            return redirect()->back()->withErrors(['email' => __($status)])->withInput($request->only('email', 'token'));
        }

        Log::info('Admin password reset: completed', ['email' => $request->email]);

        return redirect()->route('admin.login')->with('success', 'Your password has been reset. Please sign in.');
    }

}