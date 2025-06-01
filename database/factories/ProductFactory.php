<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 5000, 50000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => Category::factory(),
            'weight' => $this->faker->randomFloat(2, 50, 500),
            'color' => $this->faker->colorName(),
            'material' => $this->faker->words(2, true),
            'status' => $this->faker->randomElement(['active', 'inactive'])
        ];
    }
}