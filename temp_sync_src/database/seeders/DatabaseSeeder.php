<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat akun Super Admin sesuai permintaan
        User::create([
            'name' => 'Super Admin Pliko',
            'email' => 'plikocommunity@gmail.com',
            'password' => Hash::make('djafu12345@#'),
            'role' => 'superadmin',
        ]);

        // 2. Membuat akun Admin (data terserah)
        User::create([
            'name' => 'Admin Community',
            'email' => 'admin@jostru.com',
            'password' => Hash::make('password123'), // Password default admin
            'role' => 'admin',
        ]);

        // 3. Membuat 10 akun Member secara acak menggunakan UserFactory
        User::factory(10)->create([
            'role' => 'member',
            // password default dari factory biasanya adalah 'password'
        ]);
    }
}