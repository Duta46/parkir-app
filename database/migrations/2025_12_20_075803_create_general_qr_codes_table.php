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
        Schema::create('general_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // The unique QR code value for general use
            $table->date('date'); // The date this QR code is valid for
            $table->boolean('is_used')->default(false); // Whether the code has been used
            $table->timestamp('expires_at'); // When the QR code expires
            $table->timestamps();

            $table->unique('code'); // Ensure QR code is unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_qr_codes');
    }
};
