<?php
// Simple script to test if image upload functionality works properly

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing image upload functionality...\n";

// Check if GD extension is available (needed for image processing)
if (extension_loaded('gd')) {
    echo "✓ GD extension is loaded\n";
} else {
    echo "✗ GD extension is NOT loaded - this could cause image processing issues\n";
}

// Check if Intervention Image service is available
if (class_exists('Intervention\Image\ImageManager')) {
    echo "✓ Intervention Image library is available\n";
} else {
    echo "✗ Intervention Image library is NOT available\n";
}

// Check storage disk accessibility
try {
    if (Storage::disk('public')->exists('.')) {
        echo "✓ Public storage disk is accessible\n";
    } else {
        echo "✗ Public storage disk is NOT accessible\n";
    }
} catch (Exception $e) {
    echo "✗ Error accessing public storage: " . $e->getMessage() . "\n";
}

// Check if profile_photos directory exists or can be created
$directory = 'profile_photos';
try {
    if (Storage::disk('public')->exists($directory)) {
        echo "✓ Public storage directory 'profile_photos' exists\n";
    } else {
        if (Storage::disk('public')->makeDirectory($directory)) {
            echo "✓ Public storage directory 'profile_photos' was created successfully\n";
        } else {
            echo "✗ Could not create 'profile_photos' directory in public storage\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error with profile_photos directory: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";