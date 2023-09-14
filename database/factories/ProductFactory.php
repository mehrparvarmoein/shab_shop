<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'          => fake()->word(),
            'user_id'        => 1,
            'price'          => fake()->numberBetween(1000,999999999),
            'shipping_price' => fake()->numberBetween(1000,99999),
        ];
    }
}
