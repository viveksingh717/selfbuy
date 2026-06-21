<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        $adminDetails = Auth::guard('admin')->user();
        return view('admin.dashboard', compact('adminDetails'));
    }

    public function chat() {
        $adminDetails = Auth::guard('admin')->user();
        return view('admin.chats.chat', compact('adminDetails'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Log the user out of the 'admin' guard

        $request->session()->invalidate(); // Invalidate the session

        $request->session()->regenerateToken(); // Regenerate the CSRF token to prevent CSRF attacks

        return redirect()->route('admin.login')->with('status', 'Successfully logged out!');

    }
}
