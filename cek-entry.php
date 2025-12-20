<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check parking entry ID 1
$entry = DB::table('parking_entries')->where('id', 1)->first();

if ($entry) {
    echo "Entry ID 1 found:\n";
    echo "ID: " . $entry->id . "\n";
    echo "Kode Parkir: " . $entry->kode_parkir . "\n";
    echo "User ID: " . $entry->user_id . "\n";
    echo "QR Code ID: " . ($entry->qr_code_id ?? 'NULL') . "\n";
    echo "General QR Code ID: " . ($entry->general_qr_code_id ?? 'NULL') . "\n";
    echo "Entry Time: " . $entry->entry_time . "\n";
} else {
    echo "Entry ID 1 not found\n";
}