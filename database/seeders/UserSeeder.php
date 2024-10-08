<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder untuk Admin
        User::create([
            'name' => 'Admin User',
            'firstname' => 'Admin',
            'lastname' => 'User',
            'phone' => '08123456789',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // ganti dengan password yang diinginkan
            'avatar' => null,
            'is_admin' => true,
            'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        ]);

        // Seeder untuk User Biasa
        User::create([
            'name' => 'Regular User',
            'firstname' => 'Regular',
            'lastname' => 'User',
            'phone' => '08987654321',
            'email' => 'user@example.com',
            'password' => bcrypt('12345678'), // ganti dengan password yang diinginkan
            'avatar' => null,
            'is_admin' => false,
            'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        ]);
    }
}
