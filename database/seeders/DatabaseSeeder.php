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
        User::updateOrCreate(
            ['email' => 'plikocommunity@gmail.com'],
            [
                'name' => 'Super Admin Pliko',
                'password' => Hash::make('djafu12345@#'),
                'role' => 'superadmin',
            ]
        );

        // 2. Membuat akun Admin (data terserah)
        User::updateOrCreate(
            ['email' => 'admin@jostru.com'],
            [
                'name' => 'Admin Community',
                'password' => Hash::make('password123'), // Password default admin
                'role' => 'admin',
            ]
        );

        // 3. Membuat 10 akun Member secara acak menggunakan UserFactory
        // Only seed if no members exist to prevent duplicating 10 random users every time
        if (User::where('role', 'member')->count() == 0) {
            User::factory(10)->create([
                'role' => 'member',
            ]);
        }
    }
}