<?php
// Skrip untuk mengecek konfigurasi APP_URL dengan inisialisasi aplikasi yang bersih

// Hapus semua cache yang mungkin menyimpan konfigurasi lama
array_map('unlink', glob('bootstrap/cache/config.php'));

require_once __DIR__ . '/vendor/autoload.php';

// Buat instance aplikasi Laravel
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->useEnvironmentPath($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Sekarang cek konfigurasi APP_URL
echo "APP_URL from config: " . $app['config']['app.url'] . "\n";
echo "APP_URL from env: " . $app['config']['app']->get('app.url') . "\n";
echo "Asset URL for test file: " . $app['url']->asset('storage/test.jpg') . "\n";