<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name'            => 'Admin',
                'username'        => 'admin',
                'password'        => bcrypt('12345678'),
                'identity_number' => null,
                'user_type'       => 'admin',
            ],
            [
                'name'            => 'Petugas',
                'username'        => 'petugas',
                'password'        => bcrypt('password'),
                'identity_number' => null,
                'user_type'       => 'pegawai',
            ],
            [
                'name'            => 'Rendi',
                'username'        => 'rendi',
                'password'        => bcrypt('password'),
                'identity_number' => 'NIM123456',
                'user_type'       => 'mahasiswa',
            ],
        ]);
    }
}