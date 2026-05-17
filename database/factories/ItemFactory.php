<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
           
            'title' => ucfirst($this->faker->words(3, true)), 
            
            'description' => $this->faker->paragraph(3), 
            
            'price_per_day' => $this->faker->randomFloat(2, 10, 300), 
            
            'location' => $this->faker->city(), 
            
            'status' => $this->faker->randomElement(['available', 'available', 'rented']), 
            
            'category_id' => Category::inRandomOrder()->first()->id ?? 1, 
            
            'owner_id' => User::inRandomOrder()->first()->id ?? 1, 
        ];
    }
}