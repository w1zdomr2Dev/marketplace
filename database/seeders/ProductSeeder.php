<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Kumuha ng lahat ng sellers
        $sellers = User::where('role', 'seller')->get();

        // Bawat seller — gumawa ng 3-4 products
        foreach ($sellers as $seller) {
            Product::factory(rand(3, 4))->create([
                'seller_id' => $seller->id,
            ]);
        }
    }
}