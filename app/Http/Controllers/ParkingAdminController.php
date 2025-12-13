<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\ParkingEntry;
use App\Models\ParkingExit;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingAdminController extends Controller
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Proses scan QR code pengguna sebagai keluar jika pengguna sedang aktif
     */
    public function processUserQrCodeAsExit(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            // Validasi apakah ini adalah QR code yang valid
            $qrCodeModel = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

            if (!$qrCodeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code tidak valid'
                ], 400);
            }

            // Cek apakah QR code milik pengguna (bukan umum)
            if (!$qrCodeModel->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code ini adalah QR code umum, bukan milik pengguna spesifik'
                ], 400);
            }

            // Cek apakah pengguna yang QR codenya discan sudah memiliki masuk aktif (belum keluar)
            $activeEntry = ParkingEntry::where('user_id', $qrCodeModel->user_id)
                ->whereDoesntHave('parkingExit')
                ->with('user') // Load user info
                ->first();

            if (!$activeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak memiliki catatan masuk aktif'
                ], 400);
            }

            // Jika ada entri aktif, proses sebagai keluar
            $transactionService = app(\App\Services\ParkingTransactionService::class);

            // Ambil user terkait untuk menentukan biaya parkir
            $userTarget = $activeEntry->user;
            $baseParkingFee = 1000; // Biaya dasar Rp 1000
            $calculatedParkingFee = $transactionService->calculateConditionalFee($userTarget->id, $baseParkingFee);

            // Jika belum dibayar, proses pembayaran
            if (!$transactionService->isPaid($activeEntry->id)) {
                // Buat transaksi pembayaran secara otomatis
                $transactionService->createPaymentTransaction(
                    $activeEntry->id,
                    $calculatedParkingFee,
                    'cash',
                    [
                        'paid_amount' => $calculatedParkingFee,
                        'change' => 0,
                        'note' => 'Diproses oleh admin/petugas saat scan QR code'
                    ]
                );
            }

            // Buat catatan keluar
            $parkingExit = ParkingExit::create([
                'user_id' => $activeEntry->user_id,
                'parking_entry_id' => $activeEntry->id,
                'exit_time' => Carbon::now(),
                'exit_location' => $request->exit_location ?? null,
                'parking_fee' => $calculatedParkingFee,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan keluar berhasil direkam',
                'exit' => $parkingExit,
                'kode_parkir' => $activeEntry->kode_parkir,
                'user_name' => $activeEntry->user->name,
                'parking_fee' => $calculatedParkingFee,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in processUserQrCodeAsExit: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses QR code sebagai exit'
            ], 500);
        }
    }
}