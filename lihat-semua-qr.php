<?php
// lihat-semua-qr.php
require_once 'vendor/autoload.php';

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek semua QR code di tabel
echo "Semua QR Codes di database:\n";
$allQrcodes = DB::table('qr_codes')->get();

foreach($allQrcodes as $qr) {
    echo "ID: " . $qr->id . " | Code: " . $qr->code . " | User ID: " . ($qr->user_id ?: 'NULL') . " | Date: " . $qr->date . " | Used: " . ($qr->is_used ? 'Sudah' : 'Belum') . "\n";
}

echo "\n";

// Cek semua parking entries
echo "Semua Parking Entries:\n";
$allEntries = DB::table('parking_entries')
    ->join('users', 'parking_entries.user_id', '=', 'users.id')
    ->select('parking_entries.*', 'users.name', 'users.username')
    ->get();

foreach($allEntries as $entry) {
    echo "Entry ID: " . $entry->id . " | User: " . $entry->name . " (ID: " . $entry->user_id . ") | Kode Parkir: " . $entry->kode_parkir . " | QR Code ID: " . ($entry->qr_code_id ?: 'NULL') . "\n";
}

echo "\n";

// Cek relasi antara parking entry #1 dan qr code
echo "Relasi Entry #1 dan QR Code:\n";
$entry1 = DB::table('parking_entries')
    ->leftJoin('qr_codes', 'parking_entries.qr_code_id', '=', 'qr_codes.id')
    ->select('parking_entries.*', 'qr_codes.code as qr_code', 'qr_codes.user_id as qr_user_id')
    ->where('parking_entries.id', 1)
    ->first();

if ($entry1) {
    echo "Entry ID: " . $entry1->id . "\n";
    echo "User ID: " . $entry1->user_id . " (Harusnya milik Rendi)\n";
    echo "QR Code ID: " . ($entry1->qr_code_id ?: 'NULL') . "\n";
    echo "QR Code: " . ($entry1->qr_code ?: 'NULL') . "\n";
    echo "QR User ID: " . ($entry1->qr_user_id ?: 'NULL') . " (Harusnya milik Rendi jika benar)\n";
} else {
    echo "Entry #1 tidak ditemukan\n";
}