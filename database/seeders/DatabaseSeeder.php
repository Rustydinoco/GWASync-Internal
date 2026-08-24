<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::create([
            'nia' => 'GWA-2024-003',
            'name' => 'Agus Anggota',
            'email' => 'anggota@gwasync.com',
            'password' => '12345',
            'role' => 'anggota',
            'phone' => '081234567892',
        ]);

        // 4. Buat Akun Calon Anggota (Belum punya NIA)
        User::create([
            'nia' => null,
            'name' => 'Rina Calon',
            'email' => 'calon@gwasync.com',
            'password' => '12345',
            'role' => 'calon',
            'phone' => '081234567893',
        ]);
    }
}
