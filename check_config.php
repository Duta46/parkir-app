<?php
// Script untuk mengecek konfigurasi upload PHP

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PHP Upload Configuration ===\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "\n";

echo "\n=== Laravel Storage Configuration ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "FILESYSTEM_DISK: " . config('filesystems.default') . "\n";

echo "\n=== Storage Path Information ===\n";
echo "Storage Path: " . storage_path() . "\n";
echo "Public Storage Path: " . storage_path('app/public') . "\n";
echo "Profile Photos Directory: " . storage_path('app/public/profile_photos') . "\n";

// Cek hak akses direktori
echo "\n=== Directory Permissions ===\n";
$publicDir = storage_path('app/public');
$photosDir = storage_path('app/public/profile_photos');

echo "Public Storage Dir Exists: " . (is_dir($publicDir) ? 'Yes' : 'No') . "\n";
echo "Profile Photos Dir Exists: " . (is_dir($photosDir) ? 'Yes' : 'No') . "\n";
echo "Public Storage Writable: " . (is_writable($publicDir) ? 'Yes' : 'No') . "\n";
echo "Profile Photos Writable: " . (is_writable($photosDir) ? 'Yes' : 'No') . "\n";

echo "\n=== Storage Links ===\n";
echo "Public Storage Link Target: ";
$linkTarget = public_path('storage');
if (is_link($linkTarget) || is_dir($linkTarget)) {
    echo readlink($linkTarget) ?: 'Directory exists (Windows junction)';
} else {
    echo 'Not found';
}
echo "\n";
echo "Public Path: " . public_path('storage') . "\n";