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
            'name' => 'Pradifta T. Hatmoko',
            'firstname' => 'Pradifta T.',
            'lastname' => 'Hatmoko',
            'phone' => '081325312914',
            'email' => 'pradiftat@gmail.com',
            'password' => bcrypt('P@5$w0rd'), // ganti dengan password yang diinginkan
            'avatar' => null,
            'is_admin' => true,
            'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        ]);

        User::create([
            'name' => 'IT Sytem Application',
            'firstname' => 'IT System',
            'lastname' => 'Application',
            'phone' => '08561778677',
            'email' => 'it.system.app@gmail.com',
            'password' => bcrypt('P@5$w0rd'), // ganti dengan password yang diinginkan
            'avatar' => null,
            'is_admin' => true,
            'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        ]);

        // Seeder untuk User Biasa
        // User::create([
        //     'name' => 'Regular User',
        //     'firstname' => 'Regular',
        //     'lastname' => 'User',
        //     'phone' => '08987654321',
        //     'email' => 'user@example.com',
        //     'password' => bcrypt('12345678'), // ganti dengan password yang diinginkan
        //     'avatar' => null,
        //     'is_admin' => false,
        //     'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        // ]);
        // User::create([
        //     'name' => 'Standard User',
        //     'firstname' => 'Standard',
        //     'lastname' => 'User',
        //     'phone' => '087889987456',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('12345678'), // ganti dengan password yang diinginkan
        //     'avatar' => null,
        //     'is_admin' => false,
        //     'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        // ]);
        // User::create([
        //     'name' => 'Testing User',
        //     'firstname' => 'Testing',
        //     'lastname' => 'User',
        //     'phone' => '0812121212121',
        //     'email' => 'case@example.com',
        //     'password' => bcrypt('12345678'), // ganti dengan password yang diinginkan
        //     'avatar' => null,
        //     'is_admin' => false,
        //     'email_verified_at' => Carbon::now(), // email sudah terverifikasi
        // ]);
    }
}
