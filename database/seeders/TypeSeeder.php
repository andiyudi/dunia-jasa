<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Company Profile', 'category' => 'Legal'],
            ['name' => 'Legalitas 1', 'category' => 'Legal'],
            ['name' => 'Legalitas 2', 'category' => 'Legal'],
            ['name' => 'Legalitas 3', 'category' => 'Legal'],
            ['name' => 'KAK', 'category' => 'Tender'],
            ['name' => 'BOQ', 'category' => 'Tender'],
            ['name' => 'Offer', 'category' => 'Quotation'],
        ];

        foreach ($types as $type) {
            Type::create($type); // Memasukkan 'name' dan 'category' secara bersamaan
        }
    }
}
