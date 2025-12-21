<?php
// Debug environment variables

echo "Checking environment variables:\n";

// Cek $_ENV
if (isset($_ENV['APP_URL'])) {
    echo "APP_URL in \$_ENV: " . $_ENV['APP_URL'] . "\n";
} else {
    echo "APP_URL not found in \$_ENV\n";
}

// Cek $_SERVER
if (isset($_SERVER['APP_URL'])) {
    echo "APP_URL in \$_SERVER: " . $_SERVER['APP_URL'] . "\n";
} else {
    echo "APP_URL not found in \$_SERVER\n";
}

// Cek getenv
$appUrl = getenv('APP_URL');
if ($appUrl !== false) {
    echo "APP_URL from getenv: " . $appUrl . "\n";
} else {
    echo "APP_URL not found via getenv\n";
}

// Baca file .env secara langsung
$envContent = file_get_contents(__DIR__ . '/.env');
$envLines = explode("\n", $envContent);

echo "\nActual APP_URL line in .env file:\n";
foreach ($envLines as $line) {
    if (strpos($line, 'APP_URL=') === 0) {
        echo trim($line) . "\n";
        break;
    }
}

// Coba load ulang dotenv
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Hapus cache dotenv jika ada
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();  // Gunakan safeLoad untuk tidak mengganti nilai yang sudah ada
    
    echo "\nAfter safeLoad - getenv: " . (getenv('APP_URL') ?: 'NOT FOUND') . "\n";
    
    // Coba load paksa
    $dotenv2 = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv2->load();  // Ini akan memaksa load ulang
    
    echo "After forced load - getenv: " . (getenv('APP_URL') ?: 'NOT FOUND') . "\n";
} catch (Exception $e) {
    echo "Error with Dotenv: " . $e->getMessage() . "\n";
}