<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // 1. Users muna
            CategorySeeder::class,  // 2. Categories
            ProductSeeder::class,   // 3. Products (need users + categories)
        ]);
    }
}