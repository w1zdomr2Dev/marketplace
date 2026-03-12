<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true); // "vintage leather bag"

        return [
            'seller_id'   => User::factory()->seller(), // gumawa ng seller
            'category_id' => Category::inRandomOrder()->first()->id,
            'title'       => ucwords($title),
            'slug'        => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->paragraph(3),
            'price'       => fake()->numberBetween(50, 5000),
            'stock'       => fake()->numberBetween(1, 20),
            'location'    => fake()->city(),
            'image'       => null,
            'status'      => 'active',
            'views'       => fake()->numberBetween(0, 500),
        ];
    }
}