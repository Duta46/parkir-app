<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignRoleToUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('username', 'admin')->first();
        if ($admin) {
            $admin->assignRole('Admin');
        }

        $petugas = \App\Models\User::where('username', 'petugas')->first();
        if ($petugas) {
            $petugas->assignRole('Petugas');
        }

        $user = \App\Models\User::where('username', 'rendi')->first();
        if ($user) {
            $user->assignRole('Pengguna');
        }
    }
}
