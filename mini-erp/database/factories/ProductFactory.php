<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku'                 => 'PRD-' . fake()->unique()->numerify('#####'),
            'name'                => fake()->words(3, true),
            'unit_cost'           => fake()->randomFloat(2, 10, 500),
            'stock'               => fake()->numberBetween(10, 50),
            'low_stock_threshold' => 10,
        ];
    }
}
