<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name' => 'Sigit Triyono', 'nim' => '23083010071', 'email' => '23083010071@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Jesita Dian Rahmawati', 'nim' => '23012010375', 'email' => '23012010375@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Fadila Rachnawati', 'nim' => '23012010196', 'email' => '23012010196@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Givaldhi Ramadhan', 'nim' => '23042010260', 'email' => '23042010260@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Muhammad Valentino Putra Wijaya', 'nim' => '25081010261', 'email' => '25081010261@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Muhammad Jirjis Aufa Ahdy', 'nim' => '24035010100', 'email' => '24035010100@mbgwa.com', 'division' => 'DPA', 'status' => 'Aktif'],
            ['name' => 'Bilal Achmad Ramadhan', 'nim' => '24081010182', 'email' => '24081010182@mbgwa.com', 'division' =>'BPH', 'status' => 'Aktif'],
            ['name' => 'Nadia Ayu Fernanda', 'nim' => '25046010008', 'email' => '25046010008@mbgwa.com', 'division' => 'BPH', 'status' => 'Aktif'],
            ['name' => 'Amelia Uswatun Hasanah', 'nim' => '24082010123', 'email' => '24082010123@mbgwa.com', 'division' => 'BPH', 'status' => 'Aktif'],
            ['name' => 'Nadine Nayla Santoso', 'nim' => '25011010168', 'email' => '25011010168@mbgwa.com', 'division' => 'BPH', 'status' => 'Aktif'],
            ['name' => 'Putri Aulia', 'nim' => '24045010042', 'email' => '24045010042@mbgwa.com', 'division' => 'BPH', 'status' => 'Aktif'],
            ['name' => 'Mohammad Abiyyu Toraseno Prasetya', 'nim' => '24081010329', 'email' => '24081010329@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Fauzan Radhi Putra', 'nim' => '25071010163', 'email' => '25071010163@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Raja Samudra Tandepadang', 'nim' => '24081010063', 'email' => '24081010063@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Muhammad Royan aldiansyah', 'nim' => '25081010148', 'email' => '25081010148@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Muhammad Nasrulloh Muqsith', 'nim' => '23081010174', 'email' => '23081010174@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Widya Atika Maulana', 'nim' => '25025010080', 'email' => '25025010080@mbgwa.com', 'division' => 'Sarpras', 'status' => 'Aktif'],
            ['name' => 'Arfa Istiqamah', 'nim' => '24081010267', 'email' => '24081010267@mbgwa.com', 'division' => 'Latbang', 'status' => 'Aktif'],
            ['name' => 'Hinggar Ramadhana', 'nim' => '25082010068', 'email' => '25082010068@mbgwa.com', 'division' => 'Latbang', 'status' => 'Aktif'],
            ['name' => 'Rahmadika Febrianto', 'nim' => '23025010204', 'email' => '23025010204@mbgwa.com', 'division' => 'Latbang', 'status' => 'Aktif'],
            ['name' => 'Elok Nadinda Sari', 'nim' => '25083010006', 'email' => '25083010006@mbgwa.com', 'division' => 'Latbang', 'status' => 'Aktif'],
            ['name' => 'Nabila Aulia Putri', 'nim' => '24011010167', 'email' => '24011010167@mbgwa.com', 'division' => 'Latbang', 'status' => 'Aktif'],
            ['name' => 'M. Aditya Yuniar A. M.', 'nim' => '24012010459', 'email' => '24012010459@mbgwa.com', 'division' => 'Medkraf', 'status' => 'Aktif'],
            ['name' => 'Karmelyn Loisia Sakti', 'nim' => '25041010334', 'email' => '25041010334@mbgwa.com', 'division' => 'Medkraf', 'status' => 'Aktif'],
            ['name' => 'Kirana Larasati Alferina', 'nim' => '24011010139', 'email' => '24011010139@mbgwa.com', 'division' => 'Medkraf', 'status' => 'Aktif'],
            ['name' => 'Kurnia Sari', 'nim' => '24052010053', 'email' => '24052010053@mbgwa.com', 'division' => 'Medkraf', 'status' => 'Aktif'],
            ['name' => 'Renadya Putri Aryndra', 'nim' => '24011010145', 'email' => '24011010145@mbgwa.com', 'division' => 'Humas', 'status' => 'Aktif'],
            ['name' => 'Khirza Millatia', 'nim' => '25037010012', 'email' => '25037010012@mbgwa.com', 'division' => 'Humas', 'status' => 'Aktif'],
            ['name' => 'Zahra Ekky Maydisia W.', 'nim' => '24011010127', 'email' => '24011010127@mbgwa.com', 'division' => 'Usaha & Kemitraan', 'status' => 'Aktif'],
            ['name' => 'Natria Olivia Sinaga', 'nim' => '25041010023', 'email' => '25041010023@mbgwa.com', 'division' => 'Usaha & Kemitraan', 'status' => 'Aktif'],
            ['name' => 'Salma Firliah Nur Azizah', 'nim' => '25041010265', 'email' => '25041010265@mbgwa.com', 'division' => 'Usaha & Kemitraan', 'status' => 'Aktif'],
            ['name' => 'Andi Rajwa Putri Husein', 'nim' => '25043010373', 'email' => '25043010373@mbgwa.com', 'division' => 'Usaha & Kemitraan', 'status' => 'Aktif'],
            ['name' => 'Devinta Maharani', 'nim' => '24043010332', 'email' => '24043010332@mbgwa.com', 'division' => 'Usaha & Kemitraan', 'status' => 'Aktif'],
        ];

        // Buat password default yang di-hash
        $defaultPassword = Hash::make('mbgwa123');

        foreach ($members as $member) {
            // Gunakan updateOrCreate agar tidak terjadi error duplicate email/nim jika dijalankan berulang kali
            User::updateOrCreate(
                ['nim' => $member['nim']], 
                [
                    'name'     => $member['name'],
                    'email'    => $member['email'],
                    'password' => $defaultPassword,
                    'division' => $member['division'],
                    'status'   => $member['status'],
                ]
            );
        }
    }
}