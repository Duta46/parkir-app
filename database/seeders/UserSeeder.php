<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->insert([
            [
                'name'              => 'Admin',
                'username'          => 'admin',
                'email'             => 'admin@gmail.com',
                'password'          => bcrypt('12345678'),
                'email_verified_at' => now(),
                'identity_number'   => null,
                'user_type'         => 'admin',
            ],
            [
                'name'              => 'Petugas',
                'username'          => 'petugas',
                'email'             => 'petugas@gmail.com',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
                'identity_number'   => null,
                'user_type'         => 'pegawai',
            ],
            [
                'name'              => 'Rendi',
                'username'          => 'rendi',
                'email'             => 'rendi@gmail.com',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
                'identity_number'   => 'NIM123456',
                'user_type'         => 'mahasiswa',
            ],
        ]);
    }
}
