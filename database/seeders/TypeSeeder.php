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
            'Compro',
            'Legalitas 1',
            'Legalitas 2',
            'Legalitas 3',
        ];

        foreach ($types as $type) {
            Type::create(['name' => $type]);
        }
        // Logika untuk memperbarui kolom category
        $categories = [
            'Compro' => 'Legal',
            'Legalitas 1' => 'Legal',
            'Legalitas 2' => 'Legal',
            'Legalitas 3' => 'Legal',
        ];

        foreach ($categories as $name => $category) {
            Type::where('name', $name)->update(['category' => $category]);
        }
    }
}
