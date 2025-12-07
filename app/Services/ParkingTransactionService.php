<?php

namespace App\Services;

use App\Models\ParkingEntry;
use App\Models\ParkingTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ParkingTransactionService
{
    /**
     * Generate kode parkir unik untuk entri parkir
     *
     * @return string
     */
    public function generateKodeParkir(): string
    {
        $kode = 'PK-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Pastikan kode unik
        while (ParkingEntry::where('kode_parkir', $kode)->exists()) {
            $kode = 'PK-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        }

        return $kode;
    }

    /**
     * Generate kode parkir berdasarkan ID entri yang sudah dibuat
     *
     * @param ParkingEntry $parkingEntry
     * @return string
     */
    public function generateKodeParkirFromEntry(ParkingEntry $parkingEntry): string
    {
        // Ambil ID dari parking entry
        $entryId = $parkingEntry->id;

        // Ambil nomor plat kendaraan dari entri
        $vehiclePlate = $parkingEntry->vehicle_plate_number ?? 'NO_PLATE';

        // Bersihkan nomor plat dari karakter spesial
        $cleanPlate = preg_replace('/[^A-Za-z0-9\s]/', '_', $vehiclePlate);

        // Format tanggal menjadi DDMMYY
        $formattedDate = Carbon::now()->format('dmy');

        // Gabungkan menjadi format: id-plat-tanggal
        return $entryId . '-' . $cleanPlate . '-' . $formattedDate;
    }

    /**
     * Generate kode transaksi unik
     *
     * @return string
     */
    public function generateTransactionCode(): string
    {
        $kode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(8));

        // Pastikan kode unik
        while (ParkingTransaction::where('transaction_code', $kode)->exists()) {
            $kode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(8));
        }

        return $kode;
    }
    
    /**
     * Hitung biaya parkir berdasarkan durasi
     *
     * @param Carbon $entryTime
     * @param Carbon $exitTime
     * @param int $ratePerHour Harga per jam (default 5000)
     * @return float
     */
    public function calculateParkingFee(Carbon $entryTime, Carbon $exitTime, int $ratePerHour = 5000): float
    {
        $hours = $entryTime->diffInHours($exitTime, false);
        
        // Jika lebih dari 0 jam, pastikan minimal 1 jam
        if ($hours > 0) {
            $hours = max(1, ceil($hours)); // Pembulatan ke atas
        } else {
            $hours = 1; // Minimal 1 jam
        }
        
        return $hours * $ratePerHour;
    }
    
    /**
     * Buat transaksi pembayaran parkir
     *
     * @param int $parkingEntryId
     * @param float $amount
     * @param string $paymentMethod
     * @param array $paymentDetails
     * @return ParkingTransaction
     */
    public function createPaymentTransaction(int $parkingEntryId, float $amount, string $paymentMethod = 'cash', array $paymentDetails = []): ParkingTransaction
    {
        $parkingEntry = ParkingEntry::findOrFail($parkingEntryId);
        $transactionCode = $this->generateTransactionCode();
        
        $transaction = ParkingTransaction::create([
            'user_id' => $parkingEntry->user_id,
            'parking_entry_id' => $parkingEntryId,
            'transaction_code' => $transactionCode,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'completed', // Untuk cash, langsung completed
            'paid_at' => Carbon::now(),
            'payment_details' => $paymentDetails,
        ]);
        
        return $transaction;
    }
    
    /**
     * Proses pembayaran parkir (untuk cash)
     *
     * @param int $parkingEntryId
     * @param float $paidAmount
     * @param float $expectedAmount
     * @return array
     */
    public function processCashPayment(int $parkingEntryId, float $paidAmount, float $expectedAmount): array
    {
        $parkingEntry = ParkingEntry::findOrFail($parkingEntryId);
        
        // Cek apakah sudah ada transaksi sebelumnya
        if ($parkingEntry->parkingTransaction) {
            return [
                'success' => false,
                'message' => 'Pembayaran untuk entri ini sudah pernah dilakukan',
                'transaction' => $parkingEntry->parkingTransaction
            ];
        }
        
        // Validasi jumlah pembayaran
        if ($paidAmount < $expectedAmount) {
            return [
                'success' => false,
                'message' => "Jumlah pembayaran kurang. Bayar minimal Rp " . number_format($expectedAmount, 0, ',', '.'),
                'required_amount' => $expectedAmount,
                'paid_amount' => $paidAmount
            ];
        }
        
        // Buat transaksi pembayaran
        $transaction = $this->createPaymentTransaction(
            $parkingEntryId,
            $expectedAmount,
            'cash',
            [
                'paid_amount' => $paidAmount,
                'change' => $paidAmount - $expectedAmount
            ]
        );
        
        return [
            'success' => true,
            'message' => 'Pembayaran berhasil',
            'transaction' => $transaction,
            'change' => $paidAmount - $expectedAmount
        ];
    }

    /**
     * Cek apakah entri parkir telah dibayar
     *
     * @param int $parkingEntryId
     * @return bool
     */
    public function isPaid(int $parkingEntryId): bool
    {
        $transaction = \App\Models\ParkingTransaction::where('parking_entry_id', $parkingEntryId)->first();

        return $transaction && $transaction->status === 'completed';
    }
    
}