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
            ['name' => 'Surat Kuasa', 'category' => 'Legal'],
            ['name' => 'NPWP', 'category' => 'Legal'],
            ['name' => 'Legalitas Lain-lain', 'category' => 'Legal'],
            ['name' => 'KAK/RKS', 'category' => 'Tender'],
            ['name' => 'BOQ', 'category' => 'Tender'],
            ['name' => 'Offer', 'category' => 'Quotation'],
        ];

        foreach ($types as $type) {
            Type::create($type); // Memasukkan 'name' dan 'category' secara bersamaan
        }
    }
}
