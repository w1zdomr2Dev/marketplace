<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1 Admin — alam natin ang credentials para sa testing
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@marketplace.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_active' => true,
        ]);

        // 3 Sellers
        User::factory(3)->seller()->create();

        // 5 Buyers
        User::factory(5)->create(); // buyer ang default
    }
}