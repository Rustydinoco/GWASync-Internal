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
        User::firstOrCreate(
            ['email' => 'admin@gwasync.com'],
            [
                'name'              => 'Admin MBGWA',
                'password'          => 'admin123',
                'role'              => 'pengurus',
                'status'            => 'Aktif',
                'division'          => 'BPH',
                'nim'               => '21081010001',
                'nia'               => 'MBGWA-001',
                'phone'             => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'bilalachmad@gwasync.com'],
            [
                'nia'      => '243301057',
                'name'     => 'Bilal Achmad Ramadhan',
                'password' => '12345',
                'role'     => 'ketua_umum',
                'phone'    => '081234567892',
            ]
        );

        User::firstOrCreate(
            ['email' => 'abiyyu@gwasync.com'],
            [
                'nia'      => null,
                'name'     => 'Mohammad Abiyyu Toraseno Prasetya',
                'password' => '12345',
                'role'     => 'pengurus',
                'phone'    => '081234567893',
            ]
        );
    }
}
