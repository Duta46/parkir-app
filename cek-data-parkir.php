<?php
// This script checks if the QR code exists in the database

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if the QR code exists in general_qr_codes table
$qrCode = DB::table('general_qr_codes')->where('code', 'GENERAL-201225-oObq4xs2')->first();

if ($qrCode) {
    echo "QR Code GENERAL-201225-oObq4xs2 found in general_qr_codes table:\n";
    echo "ID: " . $qrCode->id . "\n";
    echo "Code: " . $qrCode->code . "\n";
    echo "Date: " . $qrCode->date . "\n";
    echo "Expires at: " . $qrCode->expires_at . "\n";
} else {
    echo "QR Code GENERAL-201225-oObq4xs2 NOT found in general_qr_codes table\n";
    
    // Check if it exists in qr_codes table
    $qrCode2 = DB::table('qr_codes')->where('code', 'GENERAL-201225-oObq4xs2')->first();
    if ($qrCode2) {
        echo "QR Code found in qr_codes table instead:\n";
        echo "ID: " . $qrCode2->id . "\n";
        echo "Code: " . $qrCode2->code . "\n";
        echo "Date: " . $qrCode2->date . "\n";
        echo "User ID: " . ($qrCode2->user_id ?? 'NULL') . "\n";
        echo "Is used: " . ($qrCode2->is_used ? 'Yes' : 'No') . "\n";
    } else {
        echo "QR Code not found in either table\n";
    }
}