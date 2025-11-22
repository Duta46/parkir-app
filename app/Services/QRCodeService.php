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
     * Generate kode unik untuk pengguna dan tanggal
     *
     * @param User $user
     * @param Carbon $date
     * @return string
     */
    private function generateUniqueCode(User $user, Carbon $date): string
    {
        // Buat kode unik berdasarkan ID pengguna, tanggal, dan string acak
        $prefix = $user->id . '_' . $date->format('Y-m-d');
        $random = Str::random(16);

        // Gabungkan dan hash untuk membuat kode dengan panjang konsisten
        return hash('sha256', $prefix . '_' . $random);
    }

    /**
     * Validasi QR code untuk masuk
     *
     * @param string $code
     * @return QrCode|null
     */
    public function validateQRCodeForEntry(string $code): ?QrCode
    {
        $qrCode = QrCode::where('code', $code)
            ->whereDate('date', Carbon::today())
            ->where('is_used', false)
            ->with('user')
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
        }

        return $qrCode;
    }
}