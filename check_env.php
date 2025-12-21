<?php
// Skrip untuk menguji konfigurasi APP_URL secara langsung dari file .env

// Baca file .env secara manual
$envContent = file_get_contents(__DIR__ . '/.env');
$lines = explode("\n", $envContent);

echo "APP_URL values found in .env file:\n";
foreach ($lines as $line) {
    if (strpos($line, 'APP_URL=') === 0) {
        echo "  " . trim($line) . "\n";
    }
}

// Sekarang coba baca menggunakan parse_ini_string
$envArray = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Hapus kutipan jika ada
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        $envArray[$key] = $value;
    }
}

echo "\nParsed APP_URL from .env: " . ($envArray['APP_URL'] ?? 'NOT FOUND') . "\n";

// Sekarang coba dengan library Dotenv
require_once __DIR__ . '/vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    echo "Loaded APP_URL from Dotenv: " . ($_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'NOT FOUND VIA DOTENV') . "\n";
} catch (Exception $e) {
    echo "Error loading Dotenv: " . $e->getMessage() . "\n";
}