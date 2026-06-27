<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function registerAdmin($data) {
        try {
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                return ['success' => false, 'message' => 'Email already exists. Please use a different email.'];
            }

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role_type' => 1,
                'terms'     => $data['terms'],
            ]);

            return [
                'success' => true,
                'message' => 'Account created successfully! Please sign in.',
                'data'    => $user,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create account. Please try again.'. $e->getMessage(),
            ];
        }
    }
}




?>