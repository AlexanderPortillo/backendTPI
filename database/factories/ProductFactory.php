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
    public function definition()
    {
        return [
            'product_name' => $this->faker->word(),
            'category' => $this->faker->word(),
            'product_image' => $this->faker->word(),
            'supplier' => $this->faker->word(),
            'stock' => $this->faker->randomNumber(1, 9),
            'cost' => $this->faker->randomNumber(1, 9),
            'price' => $this->faker->randomNumber(1, 9),
            'description'=> $this->faker->word(),
        ];
    }
}
