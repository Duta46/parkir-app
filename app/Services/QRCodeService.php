<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QRCodeService
{
    /**
     * Generate QR code harian untuk pengguna
     *
     * @param User $user
     * @param string|null $date Tanggal untuk QR code (default hari ini)
     * @return QrCode
     */
    public function generateDailyQRCode(User $user, string $date = null): QrCode
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Cek apakah QR code sudah ada untuk pengguna dan tanggal ini
        $qrCode = QrCode::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($qrCode) {
            return $qrCode;
        }

        // Generate QR code unik
        $code = $this->generateUniqueCode($user, $date);

        // Buat QR code baru
        $qrCode = QrCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'date' => $date,
            'expires_at' => $date->endOfDay(), // Kadaluarsa di akhir hari
            'is_used' => false,
        ]);

        return $qrCode;
    }

    /**
     * Generate QR code umum harian untuk semua pengguna
     *
     * @param string|null $date Tanggal untuk QR code (default hari ini)
     * @return QrCode
     */
    public function generateDailyQRCodeUmum(string $date = null): QrCode
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Cek apakah QR code umum sudah ada untuk tanggal ini
        $qrCode = QrCode::whereNull('user_id')  // QR code umum tidak memiliki user_id spesifik
            ->whereDate('date', $date)
            ->first();

        if ($qrCode) {
            return $qrCode;
        }

        // Generate QR code unik untuk umum
        $code = $this->generateUniqueCodeForGeneral($date);

        // Buat QR code baru tanpa user_id spesifik
        $qrCode = QrCode::create([
            'user_id' => null, // Ini adalah QR code umum
            'code' => $code,
            'date' => $date,
            'expires_at' => $date->endOfDay(), // Kadaluarsa di akhir hari
            'is_used' => false,
        ]);

        return $qrCode;
    }

    /**
     * Generate kode unik untuk QR code umum
     *
     * @param Carbon $date
     * @return string
     */
    private function generateUniqueCodeForGeneral(Carbon $date): string
    {
        // Buat kode unik untuk QR code umum dengan format: GENERAL-tanggal-random
        $prefix = 'GENERAL';
        $formattedDate = $date->format('dmy');
        $random = Str::random(8);

        // Gabungkan menjadi format: GENERAL-tanggal-random
        return $prefix . '-' . $formattedDate . '-' . $random;
    }

    /**
     * Generate kode unik untuk pengguna dan tanggal sesuai format: id-plat-tanggal
     * Contoh: 1-N 1234 AB-061225
     *
     * @param User $user
     * @param Carbon $date
     * @return string
     */
    private function generateUniqueCode(User $user, Carbon $date): string
    {
        // Ambil ID pengguna
        $userId = $user->id;

        // Ambil nomor plat kendaraan pengguna, jika tidak ada gunakan placeholder
        $vehiclePlate = $user->vehicle_plate_number ?? 'NO_PLATE';

        // Bersihkan nomor plat dari karakter spesial untuk membuatnya lebih aman dalam URL dan QR code
        $cleanPlate = preg_replace('/[^A-Za-z0-9\s]/', '_', $vehiclePlate);

        // Format tanggal menjadi DDMMYY
        $formattedDate = $date->format('dmy');

        // Gabungkan menjadi format: id-plat-tanggal
        return $userId . '-' . $cleanPlate . '-' . $formattedDate;
    }

    /**
     * Validasi QR code untuk masuk - bisa digunakan oleh siapa saja
     *
     * @param string $code
     * @return QrCode|null
     */
    public function validateQRCodeForEntry(string $code): ?QrCode
    {
        $qrCode = QrCode::where('code', $code)
            ->whereDate('date', Carbon::today())
            ->where('is_used', false)
            ->first();

        return $qrCode;
    }

    /**
     * Tandai QR code sebagai telah digunakan untuk masuk
     *
     * @param QrCode $qrCode
     * @return void
     */
    public function markQRCodeAsUsed(QrCode $qrCode): void
    {
        $qrCode->update(['is_used' => true]);
    }

    /**
     * Cek apakah pengguna memiliki QR code valid untuk hari ini
     *
     * @param User $user
     * @return QrCode|null
     */
    public function getValidQRCodeForUser(User $user): ?QrCode
    {
        return QrCode::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Generate konten gambar QR code
     *
     * @param string $code
     * @param int $size
     * @return string
     */
    public function generateQRCodeImage(string $code, int $size = 200): string
    {
        try {
            // Coba generate dengan format PNG terlebih dahulu
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)
                ->format('png')
                ->generate($code);
        } catch (\BaconQrCode\Exception\RuntimeException $e) {
            // Jika gagal karena masalah Imagick, coba dengan SVG
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)
                ->format('svg')
                ->generate($code);
        } catch (\Exception $e) {
            // Jika semua pendekatan gagal, kembalikan string kosong atau kode QR dalam bentuk teks
            \Log::error('Error generating QR code image: ' . $e->getMessage(), [
                'code' => $code,
                'size' => $size,
                'error' => $e->getMessage()
            ]);

            // Kembalikan kode sebagai fallback
            return '';
        }

        return $qrCode;
    }
}