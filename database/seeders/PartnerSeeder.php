<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        // Inisialisasi Faker dengan locale Indonesia
        $faker = Faker::create('id_ID');

        // Ambil semua kategori yang tersedia
        $categories = Category::all();
        $users = User::all();

        // Membuat 10 partner dengan data acak menggunakan Faker
        for ($i = 1; $i <= 5; $i++) {
            $partner = Partner::create([
                'name' => $faker->company,
                'npwp' => $faker->regexify('[0-9]{15}'), // Nomor NPWP 15 digit acak
                'description' => $faker->sentence,
                'is_verified' => true,
            ]);

            // Assign minimal 1 category secara acak ke setiap partner
            $assignedCategories = $categories->random(rand(1, 2));
            $partner->categories()->attach($assignedCategories);

            // Untuk setiap kategori yang diassign, buat beberapa brand secara acak
            foreach ($assignedCategories as $category) {
                $brandCount = rand(1, 2); // Minimal 1 brand per kategori
                for ($j = 1; $j <= $brandCount; $j++) {
                    Brand::create([
                        'partner_id' => $partner->id,
                        'name' => $faker->word . ' ' . $category->name, // Nama brand acak berdasarkan kategori
                    ]);
                }
            }

            // Assign minimal 1 user ke setiap partner, excluding admin users
            $assignedUsers = $users->where('is_admin', false)->random(rand(1, 2));
            $partner->users()->attach($assignedUsers);
        }
    }
}

