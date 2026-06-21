<?php
namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthService {
    public function authCheck($email, $password, $remember_me) {
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password], $remember_me)) {
            $admin = Auth::guard('admin')->user();
            if ($admin->role_type == 1) {
                $data = ['success'=>true, 'message'=>'Login successfully!'];
            } else {
                Auth::guard('admin')->logout();
                $data = ['success'=>false, 'message'=>'You are not authorized user to access admin panel!'];
            }   
        }else{
            $data = ['success'=>false, 'message'=>'Incorrect email or password'];
        }

        return $data;
    }
}




?>