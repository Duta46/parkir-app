<?php
// Skrip untuk mensimulasikan proses upload di SettingsController

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Simulating the image upload process from SettingsController...\n\n";

// Create a test image
$testImagePath = storage_path('app/public/profile_photos/test_image_' . time() . '.png');
$testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
file_put_contents($testImagePath, $testImageContent);

echo "✓ Created test image at: $testImagePath\n";

// Check if the file exists in the storage
$filename = basename($testImagePath);
$relativePath = 'profile_photos/' . $filename;

if (Storage::disk('public')->exists($relativePath)) {
    echo "✓ File exists in public disk at: $relativePath\n";
    
    // Get the file size
    $size = Storage::disk('public')->size($relativePath);
    echo "  File size: {$size} bytes\n";
    
    // Generate the URL as it would be stored in the database
    $dbPath = 'storage/profile_photos/' . $filename;
    echo "  Path that would be stored in DB: $dbPath\n";
    
    // Generate the asset URL
    $assetUrl = asset($dbPath);
    echo "  Asset URL: $assetUrl\n";
    
    // Check if the file is accessible via HTTP by checking if the physical path matches the expected public path
    $publicPath = public_path('storage') . '/' . $relativePath;
    echo "  Expected public path: $publicPath\n";
    echo "  Public path exists: " . (file_exists($publicPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($publicPath)) {
        $publicSize = filesize($publicPath);
        echo "  Public path file size: {$publicSize} bytes\n";
    }
} else {
    echo "✗ File does not exist in public disk\n";
}

// Clean up test file
if (file_exists($testImagePath)) {
    unlink($testImagePath);
    echo "✓ Test file cleaned up\n";
}

echo "\nChecking storage link configuration:\n";
$storageLink = public_path('storage');
echo "Storage link exists: " . (file_exists($storageLink) ? 'Yes' : 'No') . "\n";
echo "Storage link is directory: " . (is_dir($storageLink) ? 'Yes' : 'No') . "\n";

if (is_link($storageLink)) {
    echo "Storage link is symlink: Yes\n";
    echo "Symlink target: " . readlink($storageLink) . "\n";
} else {
    echo "Storage link is symlink: No (might be a Windows junction)\n";
}

// List contents of the storage link directory
if (is_dir($storageLink)) {
    echo "Contents of storage link directory:\n";
    $files = scandir($storageLink);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - $file\n";
        }
    }
}

echo "\nTest completed.\n";