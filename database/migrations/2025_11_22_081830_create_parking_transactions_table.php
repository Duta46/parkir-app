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
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_entry_id')->constrained('parking_entries')->onDelete('cascade');
            $table->string('transaction_code')->unique(); // Kode unik untuk transaksi
            $table->decimal('amount', 10, 2); // Jumlah pembayaran
            $table->string('payment_method'); // Metode pembayaran (cash, digital, etc)
            $table->string('status')->default('pending'); // Status pembayaran (pending, completed, failed)
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran
            $table->string('payment_reference')->nullable(); // Referensi pembayaran (untuk pembayaran digital)
            $table->json('payment_details')->nullable(); // Detail pembayaran tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
