<?php
// Set variabel lingkungan secara eksplisit sebelum memuat konfigurasi
putenv('APP_URL=http://127.0.0.1:8000');
$_ENV['APP_URL'] = 'http://127.0.0.1:8000';
$_SERVER['APP_URL'] = 'http://127.0.0.1:8000';

// Sekarang lanjutkan dengan inisialisasi aplikasi
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap the kernel to load configuration
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cek nilai APP_URL
echo "APP_URL from Laravel config: " . config('app.url') . "\n";
echo "APP_URL from env helper: " . env('APP_URL') . "\n";
echo "Current getenv: " . getenv('APP_URL') . "\n";

// Sekarang buat cache konfigurasi
try {
    $exitCode = null;
    $output = null;
    exec('cd ' . escapeshellarg(__DIR__) . ' && php artisan config:cache 2>&1', $output, $exitCode);
    
    echo "\nConfig cache command result:\n";
    echo "Exit code: " . $exitCode . "\n";
    echo "Output: " . implode("\n", $output) . "\n";
    
    // Cek lagi setelah caching
    echo "\nAfter config cache:\n";
    echo "APP_URL from Laravel config: " . config('app.url') . "\n";
    echo "APP_URL from env helper: " . env('APP_URL') . "\n";
} catch (Exception $e) {
    echo "Error during config:cache: " . $e->getMessage() . "\n";
}