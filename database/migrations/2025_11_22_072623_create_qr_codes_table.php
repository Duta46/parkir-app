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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code'); // The unique QR code value
            $table->date('date'); // The date this QR code is valid for
            $table->boolean('is_used')->default(false); // Whether the code has been used for entry
            $table->timestamp('expires_at'); // When the QR code expires
            $table->timestamps();

            $table->unique(['user_id', 'date'], 'user_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
