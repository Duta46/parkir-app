<?php
// Skrip untuk menguji apakah asset URL sekarang benar

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing asset URL generation after APP_URL change...\n\n";

echo "APP_URL config: " . config('app.url') . "\n";
echo "Asset for 'storage/test.jpg': " . asset('storage/test.jpg') . "\n";
echo "Storage disk public URL for 'test.jpg': " . Storage::disk('public')->url('test.jpg') . "\n";

// Simulate the path that would be saved in database
$testPath = 'storage/profile_photos/test_image.jpg';
echo "\nSimulated profile photo path: $testPath\n";
echo "Asset URL for profile photo: " . asset($testPath) . "\n";

// Check if the storage link exists properly
echo "\nChecking storage link:\n";
$storageLink = public_path('storage');
echo "Public storage link path: $storageLink\n";
echo "Link exists: " . (file_exists($storageLink) ? 'Yes' : 'No') . "\n";
echo "Link is directory: " . (is_dir($storageLink) ? 'Yes' : 'No') . "\n";

if (is_dir($storageLink)) {
    echo "Contents of storage link directory:\n";
    $files = scandir($storageLink);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - $file\n";
        }
    }
}