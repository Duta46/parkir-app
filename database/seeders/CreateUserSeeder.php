<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user - login dengan username
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'identity_number' => null,
            'user_type' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Pegawai user - login dengan username
        User::create([
            'name' => 'Petugas Parkir',
            'username' => 'pegawai',
            'identity_number' => null,
            'user_type' => 'pegawai',
            'email' => 'pegawai@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Dosen user - login dengan NIP/NUP
        User::create([
            'name' => 'Dr. Dosen Contoh',
            'username' => 'dosen1',
            'identity_number' => 'NIP123456',
            'user_type' => 'dosen',
            'email' => 'dosen@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Mahasiswa user - login dengan NIM
        User::create([
            'name' => 'Mahasiswa Contoh',
            'username' => 'mahasiswa1',
            'identity_number' => 'NIM20230001',
            'user_type' => 'mahasiswa',
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Tambahkan beberapa contoh dosen dan mahasiswa
        User::factory()->dosen()->create([
            'name' => 'Dr. Dosen 2',
            'username' => 'dosen2',
            'email' => 'dosen2@example.com',
        ]);

        User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa 2',
            'username' => 'mahasiswa2',
            'email' => 'mahasiswa2@example.com',
        ]);

        User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa 3',
            'username' => 'mahasiswa3',
            'email' => 'mahasiswa3@example.com',
        ]);
    }
}