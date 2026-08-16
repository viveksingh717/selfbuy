<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService {

    // role_type: 1 = admin, 0 = storefront customer
    private const CUSTOMER_ROLE = 0;
    public function authCheck($email, $password, $remember_me) {
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password], $remember_me)) {
            $admin = Auth::guard('admin')->user();
            if ($admin->role_type == 1) {
                Log::info('Admin login: success', ['admin_id' => $admin->id, 'email' => $email]);
                $data = ['success'=>true, 'message'=>'Login successfully!'];
            } else {
                Auth::guard('admin')->logout();
                Log::warning('Admin login: rejected (not role_type=1)', ['admin_id' => $admin->id, 'email' => $email]);
                $data = ['success'=>false, 'message'=>'You are not authorized user to access admin panel!'];
            }
        }else{
            Log::warning('Admin login: incorrect credentials', ['email' => $email]);
            $data = ['success'=>false, 'message'=>'Incorrect email or password'];
        }

        return $data;
    }

    public function registerAdmin($data) {
        try {
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                Log::warning('Admin registration: email already exists', ['email' => $data['email']]);

                return ['success' => false, 'message' => 'Email already exists. Please use a different email.'];
            }

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role_type' => 1,
                'terms'     => $data['terms'],
            ]);

            Log::info('Admin registration: success', ['admin_id' => $user->id, 'email' => $user->email]);

            return [
                'success' => true,
                'message' => 'Account created successfully! Please sign in.',
                'data'    => $user,
            ];
        } catch (Exception $e) {
            Log::error('Admin registration failed: '.$e->getMessage(), ['email' => $data['email'] ?? null]);

            return [
                'success' => false,
                'message' => 'Failed to create account. Please try again.'. $e->getMessage(),
            ];
        }
    }

    public function registerCustomer(array $data)
    {
        try {
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                Log::warning('Customer registration: email already exists', ['email' => $data['email']]);

                return ['success' => false, 'message' => 'An account with this email already exists. Please sign in instead.'];
            }

            $user = User::create([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'password'     => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'role_type'    => self::CUSTOMER_ROLE,
                'status'       => 1,
                'is_verified'  => 0,
                'terms'        => 1,
            ]);

            Log::info('Customer registration: account created', ['user_id' => $user->id, 'email' => $user->email]);

            return [
                'success' => true,
                'message' => 'Account created successfully!',
                'data'    => $user,
            ];
        } catch (Exception $e) {
            Log::error('Customer registration failed: '.$e->getMessage(), ['email' => $data['email'] ?? null]);

            return [
                'success' => false,
                'message' => 'Failed to create account. Please try again.',
            ];
        }
    }

    public function loginCustomer(string $email, string $password, bool $rememberMe = false): array
    {
        if (! Auth::guard('web')->attempt(['email' => $email, 'password' => $password], $rememberMe)) {
            return ['success' => false, 'message' => 'Incorrect email or password.'];
        }

        $user = Auth::guard('web')->user();

        if ($user->status !== 1) {
            Auth::guard('web')->logout();

            return ['success' => false, 'message' => 'Your account has been deactivated. Please contact support.'];
        }

        return ['success' => true, 'message' => 'Logged in successfully!'];
    }

    /**
     * Check credentials without establishing a session — used ahead of OTP 2FA,
     * where the session is only established once the OTP step also succeeds.
     */
    public function verifyCredentials(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            Log::warning('Customer login: no account for email', ['email' => $email]);

            return ['success' => false, 'message' => 'Incorrect email or password.'];
        }

        if (! Hash::check($password, $user->password)) {
            Log::warning('Customer login: incorrect password', ['user_id' => $user->id, 'email' => $email]);

            return ['success' => false, 'message' => 'Incorrect email or password.'];
        }

        if ($user->status !== 1) {
            Log::warning('Customer login: account deactivated', ['user_id' => $user->id, 'email' => $email]);

            return ['success' => false, 'message' => 'Your account has been deactivated. Please contact support.'];
        }

        Log::info('Customer login: credentials verified, awaiting OTP', ['user_id' => $user->id, 'email' => $email]);

        return ['success' => true, 'message' => 'Credentials verified.', 'data' => $user];
    }
}




?>