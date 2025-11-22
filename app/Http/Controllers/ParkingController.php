<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\ParkingEntry;
use App\Models\ParkingExit;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingController extends Controller
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Menampilkan dashboard dengan QR code dan scanner
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Generate atau dapatkan QR code hari ini untuk pengguna
        $qrCodeModel = $this->qrCodeService->getValidQRCodeForUser($user);

        if (!$qrCodeModel) {
            $qrCodeModel = $this->qrCodeService->generateDailyQRCode($user);
        }

        // Dapatkan entri parkir aktif pengguna (jika ada)
        $activeEntry = \App\Models\ParkingEntry::where('user_id', $user->id)
            ->whereDoesntHave('parkingExit')
            ->with('parkingTransaction')
            ->first();

        // Generate gambar QR code
        $qrCodeImage = $this->qrCodeService->generateQRCodeImage($qrCodeModel->code);

        // Ambil statistik umum untuk admin (jika pengguna adalah admin)
        $totalUsers = 0;
        $activeEntries = 0;
        $totalExitsToday = 0;
        $totalRevenueToday = 0;

        if ($user->hasRole('Admin')) {
            $totalUsers = \App\Models\User::count();
            $activeEntries = \App\Models\ParkingEntry::whereDoesntHave('parkingExit')->count();
            $totalExitsToday = \App\Models\ParkingExit::whereDate('exit_time', today())->count();
            $totalRevenueToday = \App\Models\ParkingExit::whereDate('exit_time', today())->sum('parking_fee');
        }

        return view('dashboard', compact(
            'qrCodeModel',
            'qrCodeImage',
            'activeEntry',
            'totalUsers',
            'activeEntries',
            'totalExitsToday',
            'totalRevenueToday'
        ));
    }

    /**
     * Menampilkan QR code pengguna saat ini
     */
    public function showQRCode()
    {
        $user = Auth::user();

        // Generate atau dapatkan QR code hari ini untuk pengguna
        $qrCodeModel = $this->qrCodeService->getValidQRCodeForUser($user);

        if (!$qrCodeModel) {
            $qrCodeModel = $this->qrCodeService->generateDailyQRCode($user);
        }

        // Generate gambar QR code
        $qrCodeImage = $this->qrCodeService->generateQRCodeImage($qrCodeModel->code);

        return view('parking.qr-code', compact('qrCodeModel', 'qrCodeImage'));
    }

    /**
     * Generate QR code baru untuk hari ini
     */
    public function generateQRCode()
    {
        $user = Auth::user();
        $qrCodeModel = $this->qrCodeService->generateDailyQRCode($user);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCodeModel->code,
            'expires_at' => $qrCodeModel->expires_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Scan QR code untuk masuk
     */
    public function scanEntry(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCodeModel = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

        if (!$qrCodeModel) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak valid atau telah kadaluarsa'
            ], 400);
        }

        // Cek apakah pengguna sudah memiliki masuk aktif (belum keluar)
        $activeEntry = ParkingEntry::where('user_id', $qrCodeModel->user_id)
            ->whereDoesntHave('parkingExit')
            ->first();

        if ($activeEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna masih memiliki catatan masuk parkir tanpa keluar'
            ], 400);
        }

        // Generate kode parkir
        $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkir();

        // Buat catatan masuk parkir
        $parkingEntry = ParkingEntry::create([
            'kode_parkir' => $kodeParkir,
            'user_id' => $qrCodeModel->user_id,
            'qr_code_id' => $qrCodeModel->id,
            'entry_time' => Carbon::now(),
            'entry_location' => $request->entry_location ?? null,
            'vehicle_type' => $request->vehicle_type ?? null,
            'vehicle_plate_number' => $request->vehicle_plate_number ?? null,
        ]);

        // Tandai QR code sebagai telah digunakan
        $this->qrCodeService->markQRCodeAsUsed($qrCodeModel);

        return response()->json([
            'success' => true,
            'message' => 'Catatan masuk berhasil direkam',
            'entry' => $parkingEntry
        ]);
    }

    /**
     * Scan QR code untuk keluar
     */
    public function scanExit(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCodeModel = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

        if (!$qrCodeModel) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak valid atau telah kadaluarsa'
            ], 400);
        }

        // Cari catatan masuk parkir untuk pengguna ini yang belum keluar
        $parkingEntry = ParkingEntry::where('user_id', $qrCodeModel->user_id)
            ->whereDoesntHave('parkingExit')
            ->first();

        if (!$parkingEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan catatan masuk parkir aktif untuk pengguna ini'
            ], 400);
        }

        // Hitung biaya parkir (contoh: Rp 5.000 per jam)
        $entryTime = $parkingEntry->entry_time;
        $exitTime = Carbon::now();
        $hoursParked = $entryTime->diffInHours($exitTime);
        $parkingFee = $hoursParked > 0 ? $hoursParked * 5000 : 5000; // Minimal Rp 5.000

        // Cek apakah pembayaran sudah dilakukan
        $transactionService = app(\App\Services\ParkingTransactionService::class);

        // Jika belum ada pembayaran, cek apakah pembayaran langsung disediakan
        if (!$transactionService->isPaid($parkingEntry->id)) {
            // Jika parameter payment disediakan, proses pembayaran langsung
            if ($request->filled('payment_amount')) {
                $paymentResult = $transactionService->processCashPayment(
                    $parkingEntry->id,
                    $request->payment_amount,
                    $parkingFee
                );

                if (!$paymentResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $paymentResult['message'],
                        'kode_parkir' => $parkingEntry->kode_parkir,
                        'required_payment' => $parkingFee
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran parkir diperlukan sebelum keluar. Silakan lakukan pembayaran terlebih dahulu.',
                    'kode_parkir' => $parkingEntry->kode_parkir,
                    'required_payment' => $parkingFee,
                    'estimated_fee' => $parkingFee
                ], 400);
            }
        }

        // Buat catatan keluar parkir
        $parkingExit = ParkingExit::create([
            'user_id' => $qrCodeModel->user_id,
            'parking_entry_id' => $parkingEntry->id,
            'exit_time' => $exitTime,
            'exit_location' => $request->exit_location ?? null,
            'parking_fee' => $parkingFee,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan keluar berhasil direkam',
            'exit' => $parkingExit,
            'parking_fee' => $parkingFee
        ]);
    }
}