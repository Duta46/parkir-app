<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number')->nullable()->after('username'); // Kolom untuk NIP/NUP/NIM
            $table->enum('user_type', ['admin', 'pegawai', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('identity_number'); // Tipe pengguna
            $table->string('nim_nip_nup')->nullable()->after('identity_number'); // Kolom tambahan untuk backward compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['identity_number', 'user_type', 'nim_nip_nup']);
        });
    }
};
