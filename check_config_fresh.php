<?php
// Simulasi inisialisasi aplikasi Laravel untuk memastikan .env dibaca dengan benar

// Hapus file cache konfigurasi
if (file_exists(__DIR__.'/bootstrap/cache/config.php')) {
    unlink(__DIR__.'/bootstrap/cache/config.php');
}

// Mulai aplikasi Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap the kernel to load configuration
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cek nilai APP_URL
echo "APP_URL from Laravel config: " . config('app.url') . "\n";
echo "APP_URL from env helper: " . env('APP_URL') . "\n";

// Cek juga filesystems
echo "Filesystem public URL: " . config('filesystems.disks.public.url') . "\n";

// Coba buat URL asset
echo "Asset URL for test: " . asset('storage/test.jpg') . "\n";