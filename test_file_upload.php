<?php
// Skrip debugging untuk menguji upload file

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing file upload functionality step by step...\n\n";

// Simulate a file upload
$tempFile = tempnam(sys_get_temp_dir(), 'test_image_');
file_put_contents($tempFile, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

// Create a fake uploaded file
$fakeFile = new Symfony\Component\HttpFoundation\File\UploadedFile(
    $tempFile,
    'test_image.png',
    'image/png',
    null,
    true
);

echo "✓ Created test image file\n";

// Test validation
$validator = Validator::make(['profile_photo' => $fakeFile], [
    'profile_photo' => ['nullable', 'image', 'max:2048']
]);

if ($validator->fails()) {
    echo "✗ Validation failed: " . implode(', ', $validator->errors()->all()) . "\n";
} else {
    echo "✓ File validation passed\n";
    
    // Test storing the file using Storage facade
    try {
        $filename = time() . '_test_user.' . $fakeFile->getClientOriginalExtension();
        $path = 'profile_photos/' . $filename;
        
        // Move the file to the public storage
        $uploaded = Storage::disk('public')->putFileAs('profile_photos', $fakeFile, $filename);
        
        if ($uploaded) {
            echo "✓ File stored successfully\n";
            
            // Check if file exists in storage
            if (Storage::disk('public')->exists('profile_photos/' . $filename)) {
                echo "✓ File exists in public disk\n";
                
                // Get file info
                $size = Storage::disk('public')->size('profile_photos/' . $filename);
                $url = Storage::disk('public')->url('profile_photos/' . $filename);
                
                echo "  File size: {$size} bytes\n";
                echo "  File URL: $url\n";
                
                // Also test asset helper
                $assetUrl = asset('storage/profile_photos/' . $filename);
                echo "  Asset URL: $assetUrl\n";
            } else {
                echo "✗ File does not exist in public disk\n";
            }
            
            // Clean up test file
            Storage::disk('public')->delete('profile_photos/' . $filename);
            echo "✓ Test file cleaned up\n";
        } else {
            echo "✗ Failed to store file\n";
        }
    } catch (Exception $e) {
        echo "✗ Error storing file: " . $e->getMessage() . "\n";
    }
}

// Clean up temp file
unlink($tempFile);
echo "\nTest completed.\n";