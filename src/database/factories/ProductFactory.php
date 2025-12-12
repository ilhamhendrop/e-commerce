<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'name' => $this->faker->word(),
            'desc' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['electronics', 'fashion', 'food']),
            'price' => $this->faker->numberBetween(1000, 100000),
            'image' => 'product/image/default.jpg',
        ];
    }
}
