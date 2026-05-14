<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'category_id' => rand(1, 5),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price_per_day'=> rand(20, 500),
            'location' => fake()->city(),
            'status' => 'available',
        ];
    }
}
