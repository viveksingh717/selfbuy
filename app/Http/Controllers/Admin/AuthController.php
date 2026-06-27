<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

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
        return view('admin.auth.register');
    }

    public function register_process(Request $request)
    {
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

}