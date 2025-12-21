<?php
// Skrip untuk mensimulasikan proses upload dan verifikasi bahwa semuanya berfungsi

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Verifikasi Fungsi Upload Gambar ===\n\n";

// 1. Cek konfigurasi APP_URL
echo "1. Konfigurasi APP_URL:\n";
echo "   " . config('app.url') . "\n\n";

// 2. Simulasikan upload file
echo "2. Mencoba membuat file gambar uji...\n";

// Buat file gambar dummy
$testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
$tempFile = tempnam(sys_get_temp_dir(), 'test_upload_');
file_put_contents($tempFile, $testImageContent);

// Buat instance UploadedFile
$uploadedFile = new UploadedFile(
    $tempFile,
    'test_avatar.png',
    'image/png',
    null,
    true
);

// Simulasikan proses upload seperti di SettingsController
$filename = time() . '_test_user.' . $uploadedFile->getClientOriginalExtension();
$path = $uploadedFile->storeAs('public/profile_photos', $filename);

if ($path) {
    echo "   ✓ File berhasil disimpan sebagai: $path\n";
    
    // Cek apakah file benar-benar ada di storage
    if (Storage::disk('public')->exists('profile_photos/' . $filename)) {
        echo "   ✓ File ada di disk public\n";
        
        // Dapatkan URL asset seperti yang digunakan di view
        $assetPath = 'storage/profile_photos/' . $filename;
        $assetUrl = asset($assetPath);
        
        echo "   ✓ Path yang disimpan: $assetPath\n";
        echo "   ✓ URL asset: $assetUrl\n";
        
        // Verifikasi bahwa URL asset menggunakan host yang benar
        if (strpos($assetUrl, '127.0.0.1:8000') !== false) {
            echo "   ✓ URL asset menggunakan host yang benar\n";
        } else {
            echo "   ✗ URL asset masih menggunakan host yang salah\n";
        }
        
        // Cek apakah file bisa diakses secara fisik
        $publicPath = public_path('storage/profile_photos/' . $filename);
        if (file_exists($publicPath)) {
            echo "   ✓ File bisa diakses secara fisik di: $publicPath\n";
            echo "   ✓ File size: " . filesize($publicPath) . " bytes\n";
        } else {
            echo "   ✗ File tidak bisa diakses secara fisik\n";
        }
        
        // Hapus file uji
        Storage::disk('public')->delete('profile_photos/' . $filename);
        echo "   ✓ File uji telah dihapus\n";
    } else {
        echo "   ✗ File tidak ditemukan di disk public\n";
    }
} else {
    echo "   ✗ Gagal menyimpan file\n";
}

// Hapus file temp
unlink($tempFile);

echo "\n3. Verifikasi struktur direktori:\n";

// Cek direktori storage
$storageDir = storage_path('app/public/profile_photos');
if (is_dir($storageDir)) {
    echo "   ✓ Direktori storage/app/public/profile_photos ada\n";
} else {
    echo "   ✗ Direktori storage/app/public/profile_photos tidak ada\n";
}

// Cek link simbolik
$publicStorage = public_path('storage');
if (file_exists($publicStorage) && is_dir($publicStorage)) {
    echo "   ✓ Link simbolik public/storage ada\n";
} else {
    echo "   ✗ Link simbolik public/storage tidak ada\n";
}

echo "\n=== Kesimpulan ===\n";
echo "Jika semua indikator menunjukkan ✓, maka fungsi upload gambar seharusnya sudah berfungsi dengan benar.\n";
echo "Pastikan untuk menjalankan server Laravel dengan perintah:\n";
echo "   php artisan serve --host=127.0.0.1 --port=8000\n";