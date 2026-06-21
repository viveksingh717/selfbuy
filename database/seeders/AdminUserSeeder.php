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
        
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Vivek Singh',
                'password' => Hash::make('password'),
                'role_type' => 1
            ]
        );

        User::updateOrCreate(
            ['email' => 'rohit@gmail.com'],
            [
                'name' => 'Rohan Sharma',
                'password' => Hash::make('password'),
                'role_type' => 2
            ]
        );


        // User::insert([
        //     [
        //         'name' => 'Vivek Singh',
        //         'email' => 'admin@gmail.com',
        //         'password' => bcrypt('123456'),
        //         'role_type' => 1,
        //     ],
        //     [
        //         'name' => 'Rohan Sharma',
        //         'email' => 'rohit@gmail.com',
        //         'password' => bcrypt('123456'),
        //         'role_type' => 2,
        //     ]
        // ]);
    }
}
