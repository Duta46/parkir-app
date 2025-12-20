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
            $table->unsignedBigInteger('general_qr_code_id')->nullable()->after('qr_code_id');
            $table->foreign('general_qr_code_id')->references('id')->on('general_qr_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_entries', function (Blueprint $table) {
            $table->dropForeign(['general_qr_code_id']);
            $table->dropColumn('general_qr_code_id');
        });
    }
};
