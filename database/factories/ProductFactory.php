<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->paragraph(1),
            'quantity' => $this->faker->numberBetween(1,10),
            'status' => $this->faker->randomElement([Product::AVAILABLE_PRODUCT,Product::UNAVAILABLE_PRODUCT]),
            'image' => $this->faker->randomElement(['1.png','2.png','3.png']),
            'seller_id' => User::all()->random()->id
        ];
    }
}
