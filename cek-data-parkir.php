<?php
// cek-data-parkir.php
require_once 'vendor/autoload.php';

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek data entri parkir #1
$entry = DB::table('parking_entries')
    ->join('users', 'parking_entries.user_id', '=', 'users.id')
    ->select('parking_entries.*', 'users.name', 'users.username')
    ->where('parking_entries.id', 1)
    ->first();

if ($entry) {
    echo "Entri ID: " . $entry->id . "\n";
    echo "User ID: " . $entry->user_id . "\n";
    echo "Nama User: " . $entry->name . "\n";
    echo "Username: " . $entry->username . "\n";
    echo "Kode Parkir: " . $entry->kode_parkir . "\n";
    echo "Waktu Masuk: " . $entry->entry_time . "\n";
    
    // Cek apakah sudah ada exit
    $hasExit = DB::table('parking_exits')->where('parking_entry_id', $entry->id)->exists();
    echo "Status: " . ($hasExit ? "Sudah Keluar" : "Belum Keluar") . "\n";
} else {
    echo "Entri dengan ID 1 tidak ditemukan\n";
}

// Cek QR code terkait dengan entri parkir ini
$qrCode = DB::table('qr_codes')
    ->join('parking_entries', 'qr_codes.id', '=', 'parking_entries.qr_code_id')
    ->select('qr_codes.*')
    ->where('parking_entries.id', 1)
    ->first();

if ($qrCode) {
    echo "\nQR Code terkait dengan entri ID 1:\n";
    echo "QR Code ID: " . $qrCode->id . "\n";
    echo "QR Code: " . $qrCode->code . "\n";
    echo "Tanggal: " . $qrCode->date . "\n";
    echo "Expired at: " . $qrCode->expires_at . "\n";
    echo "Is used: " . ($qrCode->is_used ? "Sudah" : "Belum") . "\n";
    echo "User ID: " . ($qrCode->user_id ? $qrCode->user_id : "Umum") . "\n";
} else {
    echo "\nTidak ditemukan QR code terkait dengan entri ID 1\n";
}