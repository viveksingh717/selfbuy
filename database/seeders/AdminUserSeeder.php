<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // The password comes from .env, never from this file - the repo may be public.
        $password = env('ADMIN_SEED_PASSWORD');
        if (! $password || strlen($password) < 8) {
            $this->command?->error('Set ADMIN_SEED_PASSWORD in .env (at least 12 characters) before running AdminUserSeeder.');

            return;
        }

        User::updateOrCreate(
            ['email' => 'viveksmacbook07@gmail.com'],
            [
                'name' => 'Vivek Singh',
                'password' => Hash::make($password),
                'role_type' => 1, // 1 = admin, 0 = customer
            ]
        );

        User::updateOrCreate(
            ['email' => 'vs4092344@gmail.com'],
            [
                'name' => 'Admin Ruler',
                'password' => Hash::make($password),
                'role_type' => 1,
            ]
        );
    }
}
