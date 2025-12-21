<?php
// Skrip untuk menguji proses upload gambar dengan perubahan terbaru

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Pengujian Proses Upload Gambar Baru ===\n\n";

// 1. Cek konfigurasi APP_URL
echo "1. Konfigurasi APP_URL:\n";
echo "   " . config('app.url') . "\n\n";

// 2. Simulasikan upload file dengan metode baru
echo "2. Mencoba membuat file gambar uji dengan metode baru...\n";

// Buat file gambar dummy
$testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
$tempFile = tempnam(sys_get_temp_dir(), 'test_upload_new_');
file_put_contents($tempFile, $testImageContent);

// Buat instance UploadedFile
$uploadedFile = new UploadedFile(
    $tempFile,
    'test_avatar_new.png',
    'image/png',
    null,
    true
);

// Simulasikan proses upload seperti di SettingsController yang baru
$filename = time() . '_test_user_new.' . $uploadedFile->getClientOriginalExtension();
$path = $uploadedFile->storeAs('profile_photos', $filename, 'public');

if ($path) {
    echo "   ✓ File berhasil disimpan sebagai: $path\n";
    
    // Cek apakah file benar-benar ada di storage public
    if (Storage::disk('public')->exists($path)) {
        echo "   ✓ File ada di disk public\n";
        
        // Dapatkan URL asset seperti yang digunakan di view
        $assetPath = 'storage/' . $path;
        $assetUrl = asset($assetPath);
        
        echo "   ✓ Path yang disimpan: $assetPath\n";
        echo "   ✓ URL asset: $assetUrl\n";
        
        // Verifikasi bahwa URL asset menggunakan host yang benar
        if (strpos($assetUrl, '127.0.0.1:8000') !== false) {
            echo "   ✓ URL asset menggunakan host yang benar\n";
        } else {
            echo "   ⚠ URL asset mungkin masih menggunakan host yang salah\n";
        }
        
        // Cek apakah file bisa diakses secara fisik di public/storage
        $publicPath = public_path('storage/' . $path);
        if (file_exists($publicPath)) {
            echo "   ✓ File bisa diakses secara fisik di: $publicPath\n";
            echo "   ✓ File size: " . filesize($publicPath) . " bytes\n";
        } else {
            echo "   ✗ File tidak bisa diakses secara fisik di public/storage\n";
        }
        
        // Hapus file uji
        Storage::disk('public')->delete($path);
        echo "   ✓ File uji telah dihapus dari disk public\n";
    } else {
        echo "   ✗ File tidak ditemukan di disk public\n";
    }
} else {
    echo "   ✗ Gagal menyimpan file\n";
}

// Hapus file temp
unlink($tempFile);

echo "\n3. Menguji fungsi penghapusan file (mock)...\n";

// Simulasikan penghapusan file
$testFileForDeletion = 'profile_photos/test_deletion_file.png';
// Buat file dummy di disk public
Storage::disk('public')->put($testFileForDeletion, 'dummy content');

if (Storage::disk('public')->exists($testFileForDeletion)) {
    echo "   ✓ File dummy berhasil dibuat untuk pengujian penghapusan\n";
    
    // Simulasikan penghapusan seperti di controller
    $filePath = str_replace('storage/', 'public/', 'storage/' . $testFileForDeletion);
    if (Storage::disk('public')->exists($testFileForDeletion)) {
        Storage::disk('public')->delete($testFileForDeletion);
        if (!Storage::disk('public')->exists($testFileForDeletion)) {
            echo "   ✓ File berhasil dihapus seperti di controller\n";
        } else {
            echo "   ✗ File gagal dihapus\n";
        }
    } else {
        echo "   ✗ File tidak ditemukan untuk dihapus\n";
    }
} else {
    echo "   ✗ File dummy gagal dibuat\n";
}

echo "\n=== Ringkasan Perubahan ===\n";
echo "1. File sekarang disimpan menggunakan: \$photo->storeAs('profile_photos', \$filename, 'public')\n";
echo "2. Path disimpan ke database sebagai: 'storage/profile_photos/' . \$filename\n";
echo "3. Fungsi hapus file diperbarui untuk bekerja dengan disk 'public'\n";
echo "4. View tetap menggunakan asset() helper secara normal\n\n";

echo "=== Rekomendasi ===\n";
echo "Setelah restart server, fungsi upload gambar seharusnya berjalan dengan baik.\n";
echo "Pastikan untuk menjalankan server Laravel dengan perintah:\n";
echo "   php artisan serve --host=127.0.0.1 --port=8000\n";