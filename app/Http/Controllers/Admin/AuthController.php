<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function __construct() {
        $this->authService = new AuthService;
    }

    public function login() {
        return view('admin.auth.login');
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
            return redirect()->route('admin.dashboard')->with('success', $authCheck['message']);
        }else{
            return Redirect::back()->with('error', $authCheck['message'])
            ->withInput($request->only(['email','remember_me']));
        }

        return Redirect::back()->with('error', 'Something went wrong, please check inputs and try again')
            ->withInput($request->only(['email','remember_me']));
    }
}