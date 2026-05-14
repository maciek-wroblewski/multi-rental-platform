<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Bikes'
        ]);

        Category::create([
            'name' => 'Books'
        ]);

        Category::create([
            'name' => 'Games'
        ]);

        Category::create([
            'name' => 'Movies'
        ]);

        Category::create([
            'name' => 'Cars'
        ]);
    }
}
