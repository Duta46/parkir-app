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
                'vehicle_type'    => null,
                'vehicle_plate_number' => null,
            ],
            [
                'name'            => 'Petugas',
                'username'        => 'petugas',
                'password'        => bcrypt('password'),
                'identity_number' => null,
                'user_type'       => 'pegawai',
                'vehicle_type'    => null,
                'vehicle_plate_number' => null,
            ],
            [
                'name'            => 'Pegawai Contoh',
                'username'        => 'pegawai',
                'password'        => bcrypt('password'),
                'identity_number' => 'NIP123456',
                'user_type'       => 'pegawai',
                'vehicle_type'    => null,  
                'vehicle_plate_number' => null,
            ],
            [
                'name'            => 'Rendi',
                'username'        => 'rendi',
                'password'        => bcrypt('password'),
                'identity_number' => 'NIM123456',
                'user_type'       => 'mahasiswa',
                'vehicle_type'    => 'motor',
                'vehicle_plate_number' => 'N 1234 AB',
            ],
        ]);
    }
}
