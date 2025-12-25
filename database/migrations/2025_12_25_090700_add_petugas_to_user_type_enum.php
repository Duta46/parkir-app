<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the user_type enum to include 'petugas'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('mahasiswa', 'dosen', 'pegawai', 'petugas', 'admin')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum without 'petugas'
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('mahasiswa', 'dosen', 'pegawai', 'admin')");
    }
};
