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
        Schema::create('parking_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_entry_id')->constrained('parking_entries')->onDelete('cascade'); // Reference to the entry
            $table->timestamp('exit_time'); // When the user exited
            $table->string('exit_location')->nullable(); // Location where exit happened
            $table->decimal('parking_fee', 10, 2)->nullable(); // Fee charged for parking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_exits');
    }
};
