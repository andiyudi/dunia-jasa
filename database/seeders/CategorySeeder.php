<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Manufactur',
            'General Supplier',
            'Specialist Supplier',
            'Contractor',
            'Consultant',
            'Service etc'
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}