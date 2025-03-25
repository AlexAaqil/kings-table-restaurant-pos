<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 real users with specific details
        $admin_password = env('DEFAULT_ADMIN_PASSWORD');
        $user_password = env('DEFAULT_USER_PASSWORD');

        $realUsers = [
            [
                'first_name' => 'Admin',
                'last_name' => 'Root',
                'email' => 'admin_root@gmail.com',
                'password' => $admin_password,
                'user_level' => 0,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => $admin_password,
                'user_level' => 1,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'cashier@gmail.com',
                'password' => $user_password,
                'user_level' => 2,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($realUsers as $user) {
            User::create($user);
        }

        // Create 10 fake users
        // User::factory(10)->create();
    }
}
