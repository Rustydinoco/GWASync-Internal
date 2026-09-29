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
            'nia' => '243301057',
            'name' => 'Bilal Achmad Ramadhan',
            'email' => 'bilalachmad@gwasync.com',
            'password' => '12345',
            'role' => 'ketua_umum',
            'phone' => '081234567892',
        ]);

        // 4. Buat Akun Calon Anggota (Belum punya NIA)
        User::create([
            'nia' => null,
            'name' => 'Mohammad Abiyyu Toraseno Prasetya',
            'email' => 'abiyyu@gwasync.com',
            'password' => '12345',
            'role' => 'pengurus',
            'phone' => '081234567893',
        ]);
    }
}
