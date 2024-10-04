<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
    }
}
