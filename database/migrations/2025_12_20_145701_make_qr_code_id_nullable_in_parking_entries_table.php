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
        Schema::table('parking_entries', function (Blueprint $table) {
            // Make qr_code_id nullable to support general QR codes
            $table->unsignedBigInteger('qr_code_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_entries', function (Blueprint $table) {
            // Revert qr_code_id to non-nullable (default behavior)
            $table->unsignedBigInteger('qr_code_id')->nullable(false)->change();
        });
    }
};
