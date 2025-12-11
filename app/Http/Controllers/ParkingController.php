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
        try {
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

            // Periksa apakah QR code adalah untuk pengguna tertentu atau umum
            $user = Auth::user();

            // Jika QR code memiliki pemilik (bukan umum) dan bukan milik admin/petugas, maka hanya admin/petugas atau user dengan user_type pegawai yang bisa discan
            if ($qrCodeModel->user_id && !$user->hasRole(['Admin', 'Petugas']) && $user->user_type !== 'pegawai') {
                // Ambil user pemilik QR code untuk mengecek apakah dia admin/petugas
                $qrCodeOwner = \App\Models\User::find($qrCodeModel->user_id);

                // Jika tidak bisa menemukan pemilik QR code, tolak
                if (!$qrCodeOwner) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pemilik QR code tidak ditemukan'
                    ], 400);
                }

                // Jika pemilik QR code bukan admin/petugas (yaitu mahasiswa/dosen), hanya admin/petugas yang bisa discan
                if (!in_array($qrCodeOwner->user_type, ['admin', 'pegawai'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda hanya dapat memindai QR code milik Anda sendiri'
                    ], 400);
                }
            }

            // Tentukan user_id untuk entri parkir
            // Jika QR code milik admin/petugas, maka entri dibuat untuk pengguna yang sedang login (karena mereka yang menggunakan QR code admin/petugas untuk masuk)
            // Jika QR code umum (tanpa user_id), gunakan user yang sedang login
            // Jika QR code milik pengguna tertentu dan bukan admin/petugas, maka hanya admin/petugas yang bisa scan dan entri tetap untuk pemilik QR code
            if ($qrCodeModel->user_id) {
                $qrCodeOwner = \App\Models\User::find($qrCodeModel->user_id);
                if ($qrCodeOwner && (in_array($qrCodeOwner->user_type, ['admin', 'pegawai']) || $qrCodeOwner->hasRole(['Admin', 'Petugas']))) {
                    // Jika QR code milik admin/petugas, entri dibuat untuk pengguna yang sedang login
                    $entryUserId = $user->id;
                } else {
                    // Jika QR code milik pengguna biasa, entri dibuat untuk pemilik QR code (hanya bisa discan oleh admin/petugas)
                    $entryUserId = $qrCodeModel->user_id;
                }
            } else {
                // Jika QR code umum, entri dibuat untuk pengguna yang sedang login
                $entryUserId = $user->id;
            }

            // Cek apakah pengguna sudah memiliki masuk aktif (belum keluar)
            $activeEntry = ParkingEntry::where('user_id', $entryUserId)
                ->whereDoesntHave('parkingExit')
                ->first();

            if ($activeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna masih memiliki catatan masuk parkir tanpa keluar'
                ], 400);
            }

            // Ambil data kendaraan dari pengguna jika tersedia
            $userEntry = \App\Models\User::find($entryUserId);
            $vehicleType = $userEntry ? $userEntry->vehicle_type : ($request->vehicle_type ?? null);
            $vehiclePlateNumber = $userEntry ? $userEntry->vehicle_plate_number : ($request->vehicle_plate_number ?? null);

            // Buat catatan masuk parkir sementara tanpa kode parkir
            $parkingEntry = ParkingEntry::create([
                'user_id' => $entryUserId,
                'qr_code_id' => $qrCodeModel->id,
                'entry_time' => Carbon::now(),
                'entry_location' => $request->entry_location ?? null,
                'vehicle_type' => $vehicleType,
                'vehicle_plate_number' => $vehiclePlateNumber,
            ]);

            // Generate kode parkir berdasarkan ID parking entry yang baru dibuat
            $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkirFromEntry($parkingEntry);

            // Perbarui kode parkir dengan format baru
            $parkingEntry->update([
                'kode_parkir' => $kodeParkir
            ]);

            // Tandai QR code sebagai telah digunakan (hanya untuk QR code umum, karena untuk per pengguna hanya bisa digunakan sekali oleh pemiliknya)
            if (!$qrCodeModel->user_id) {
                $this->qrCodeService->markQRCodeAsUsed($qrCodeModel);
            }

            return response()->json([
                'success' => true,
                'message' => 'Catatan masuk berhasil direkam',
                'entry' => $parkingEntry
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in scanEntry: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses barcode masuk. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * Menampilkan halaman scan barcode
     */
    public function showScanPage()
    {
        try {
            $user = Auth::user();

            // Ambil QR code milik user jika sudah ada
            $currentQRCode = $this->qrCodeService->getValidQRCodeForUser($user);

            if (!$currentQRCode) {
                $currentQRCode = $this->qrCodeService->generateDailyQRCode($user);
            }

            $qrCodeImage = $this->qrCodeService->generateQRCodeImage($currentQRCode->code);

            return view('parking.scan-barcode', compact('currentQRCode', 'qrCodeImage'));
        } catch (\Exception $e) {
            \Log::error('Error in showScanPage: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan saat memuat halaman scan.');
        }
    }

    /**
     * Menampilkan halaman scan barcode khusus untuk admin/petugas
     */
    public function showAdminScanPage()
    {
        try {
            $user = Auth::user();

            // Hanya untuk admin dan petugas
            if (!$user->hasRole(['Admin', 'Petugas'])) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }

            // Ambil QR code milik admin/petugas jika sudah ada
            $currentQRCode = $this->qrCodeService->getValidQRCodeForUser($user);

            if (!$currentQRCode) {
                $currentQRCode = $this->qrCodeService->generateDailyQRCode($user);
            }

            $qrCodeImage = $this->qrCodeService->generateQRCodeImage($currentQRCode->code);

            return view('parking.admin-scan-barcode', compact('currentQRCode', 'qrCodeImage'));
        } catch (\Exception $e) {
            \Log::error('Error in showAdminScanPage: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan saat memuat halaman scan admin.');
        }
    }

    /**
     * Proses scan barcode untuk masuk parkir
     */
    public function scanBarcode(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $user = Auth::user();

            // Validasi QR code yang discan
            $qrCodeModel = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

            if (!$qrCodeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code tidak valid atau telah kadaluarsa'
                ], 400);
            }

            // Jika QR code memiliki pemilik (bukan umum) dan bukan milik admin/petugas, maka hanya admin/petugas atau user dengan user_type pegawai yang bisa discan
            if ($qrCodeModel->user_id && !$user->hasRole(['Admin', 'Petugas']) && $user->user_type !== 'pegawai') {
                // Ambil user pemilik QR code untuk mengecek apakah dia admin/petugas
                $qrCodeOwner = \App\Models\User::find($qrCodeModel->user_id);

                // Jika tidak bisa menemukan pemilik QR code, tolak
                if (!$qrCodeOwner) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pemilik QR code tidak ditemukan'
                    ], 400);
                }

                // Jika pemilik QR code bukan admin/petugas (yaitu mahasiswa/dosen), hanya admin/petugas yang bisa discan
                if (!in_array($qrCodeOwner->user_type, ['admin', 'pegawai'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda hanya dapat memindai QR code milik Anda sendiri'
                    ], 400);
                }
            }

            // Tentukan user_id untuk entri parkir
            // Jika QR code milik admin/petugas, maka entri dibuat untuk pengguna yang sedang login (karena mereka yang menggunakan QR code admin/petugas untuk masuk)
            // Jika QR code umum (tanpa user_id), gunakan user yang sedang login
            // Jika QR code milik pengguna tertentu dan bukan admin/petugas, maka hanya admin/petugas yang bisa scan dan entri tetap untuk pemilik QR code
            if ($qrCodeModel->user_id) {
                $qrCodeOwner = \App\Models\User::find($qrCodeModel->user_id);
                if ($qrCodeOwner && (in_array($qrCodeOwner->user_type, ['admin', 'pegawai']) || $qrCodeOwner->hasRole(['Admin', 'Petugas']))) {
                    // Jika QR code milik admin/petugas, entri dibuat untuk pengguna yang sedang login
                    $entryUserId = $user->id;
                } else {
                    // Jika QR code milik pengguna biasa, entri dibuat untuk pemilik QR code (hanya bisa discan oleh admin/petugas)
                    $entryUserId = $qrCodeModel->user_id;
                }
            } else {
                // Jika QR code umum, entri dibuat untuk pengguna yang sedang login
                $entryUserId = $user->id;
            }

            // Cek apakah pengguna yang QR codenya discan sudah memiliki masuk aktif (belum keluar)
            $activeEntry = ParkingEntry::where('user_id', $entryUserId)
                ->whereDoesntHave('parkingExit')
                ->first();

            if ($activeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna masih memiliki catatan masuk parkir tanpa keluar'
                ], 400);
            }

            // Ambil data kendaraan dari pengguna jika tersedia
            $userEntry = \App\Models\User::find($entryUserId);
            $vehicleType = $userEntry ? $userEntry->vehicle_type : null;
            $vehiclePlateNumber = $userEntry ? $userEntry->vehicle_plate_number : null;

            // Buat catatan masuk parkir sementara tanpa kode parkir
            $parkingEntry = ParkingEntry::create([
                'user_id' => $entryUserId,
                'qr_code_id' => $qrCodeModel->id,
                'entry_time' => Carbon::now(),
                'entry_location' => $request->entry_location ?? null,
                'vehicle_type' => $vehicleType,
                'vehicle_plate_number' => $vehiclePlateNumber,
            ]);

            // Generate kode parkir berdasarkan ID parking entry yang baru dibuat
            $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkirFromEntry($parkingEntry);

            // Perbarui kode parkir dengan format baru
            $parkingEntry->update([
                'kode_parkir' => $kodeParkir
            ]);

            // Update status QR code sebagai digunakan (jika ini adalah QR code umum)
            if (!$qrCodeModel->user_id) {
                $this->qrCodeService->markQRCodeAsUsed($qrCodeModel);
            }

            // Generate QR code baru untuk pengguna saat ini untuk digunakan saat keluar
            $userQRCode = $this->qrCodeService->generateDailyQRCode($user);

            // Ambil gambar QR code untuk user saat ini
            $qrCodeImage = $this->qrCodeService->generateQRCodeImage($userQRCode->code);

            return response()->json([
                'success' => true,
                'message' => 'Catatan masuk berhasil direkam',
                'entry' => $parkingEntry,
                'user_qr_code' => $userQRCode->code,
                'user_qr_code_image' => $qrCodeImage
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in scanBarcode: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses barcode. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * Proses keluar parkir - bisa menggunakan kode parkir atau scan barcode pengguna
     */
    public function scanExit(Request $request)
    {
        try {
            // Validasi input - bisa kode_parkir atau qr_code
            $request->validate([
                'kode_parkir' => 'nullable|string',
                'qr_code' => 'nullable|string',
            ]);

            // Validasi: hanya admin/petugas atau user dengan user_type pegawai yang bisa memproses keluar parkir
            $user = Auth::user();
            if (!$user->hasRole(['Admin', 'Petugas']) && $user->user_type !== 'pegawai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin atau petugas yang dapat memproses keluar parkir'
                ], 400);
            }

            // Tentukan metode scan - apakah menggunakan kode parkir atau barcode
            $parkingEntry = null;

            if ($request->filled('kode_parkir')) {
                // Cari catatan masuk parkir berdasarkan kode parkir
                $parkingEntry = ParkingEntry::where('kode_parkir', $request->kode_parkir)
                    ->whereDoesntHave('parkingExit')
                    ->first();
            } elseif ($request->filled('qr_code')) {
                // Cari berdasarkan QR code milik pengguna
                $qrCode = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

                if ($qrCode) {
                    // Jika QR code valid, cari parking entry berdasarkan QR code ID
                    $parkingEntry = ParkingEntry::where('qr_code_id', $qrCode->id)
                        ->whereDoesntHave('parkingExit')
                        ->first();

                    // Validasi tambahan: hanya admin/petugas yang bisa memindai QR code milik pengguna
                    if ($qrCode->user_id) {
                        // Ini adalah QR code milik pengguna tertentu, hanya admin/petugas atau user dengan user_type pegawai yang bisa memindai
                        if (!$user->hasRole(['Admin', 'Petugas']) && $user->user_type !== 'pegawai') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Hanya admin atau petugas yang dapat memindai QR code milik pengguna untuk proses keluar parkir'
                            ], 400);
                        }
                    }
                }
            }

            if (!$parkingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode parkir atau barcode tidak valid, atau pengguna sudah keluar'
                ], 400);
            }

            // Ambil user untuk menentukan biaya parkir
            $user = $parkingEntry->user;
            $baseParkingFee = 1000; // Ganti biaya dari 2000 ke 1000 sesuai permintaan

            $transactionService = app(\App\Services\ParkingTransactionService::class);

            // Hitung biaya parkir berdasarkan kebijakan: 1x bayar per hari untuk user non-admin/petugas
            // (mahasiswa, dosen, dan pegawai - tapi bukan admin/petugas)
            $parkingFee = $transactionService->calculateConditionalFee($user->id, $baseParkingFee);

            // Cek apakah pembayaran sudah dilakukan
            if (!$transactionService->isPaid($parkingEntry->id)) {
                // Jika parameter payment disediakan, proses pembayaran langsung
                if ($request->filled('payment_amount')) {
                    $paymentAmount = $request->payment_amount;

                    // Jika biaya parkir adalah 0, kita tetap perlu menangani pembayaran
                    if ($parkingFee == 0) {
                        // Jika tidak perlu bayar, buat transaksi dengan jumlah 0
                        $paymentResult = $transactionService->createPaymentTransaction(
                            $parkingEntry->id,
                            0,
                            'cash',
                            [
                                'paid_amount' => $paymentAmount,
                                'change' => $paymentAmount // Kembalikan semua sebagai kembalian
                            ]
                        );

                        $paymentResult = [
                            'success' => true,
                            'message' => 'Pembayaran berhasil (gratis berdasarkan kebijakan harian)',
                            'transaction' => $paymentResult
                        ];
                    } else {
                        // Proses pembayaran normal
                        $paymentResult = $transactionService->processCashPayment(
                            $parkingEntry->id,
                            $paymentAmount,
                            $parkingFee
                        );
                    }

                    if (!$paymentResult['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => $paymentResult['message'],
                            'kode_parkir' => $parkingEntry->kode_parkir,
                            'required_payment' => $parkingFee
                        ], 400);
                    }
                } else {
                    // Jika biaya parkir adalah 0, pengguna tidak perlu membayar
                    if ($parkingFee == 0) {
                        // Buat transaksi gratis secara otomatis
                        $transactionService->createPaymentTransaction(
                            $parkingEntry->id,
                            0,
                            'cash',
                            [
                                'paid_amount' => 0,
                                'change' => 0,
                                'note' => 'Gratis karena sudah membayar hari ini'
                            ]
                        );
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
            }

            // Buat catatan keluar parkir
            $parkingExit = ParkingExit::create([
                'user_id' => $parkingEntry->user_id,
                'parking_entry_id' => $parkingEntry->id,
                'exit_time' => Carbon::now(),
                'exit_location' => $request->exit_location ?? null,
                'parking_fee' => $parkingFee,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan keluar berhasil direkam',
                'exit' => $parkingExit,
                'parking_fee' => $parkingFee,
                'user_name' => $parkingEntry->user->name,
                'user_type' => $user->user_type,
                'has_paid_today' => $transactionService->hasPaidToday($user->id),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in scanExit: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses barcode keluar. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    /**
     * Update waktu keluar parkir menggunakan PUT method
     * Fungsi ini untuk memperbarui data waktu keluar dan status pengguna
     */
    public function adminUpdateExit(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $user = Auth::user();

            // Cari entri parkir berdasarkan kode parkir
            $parkingEntry = \App\Models\ParkingEntry::where('kode_parkir', $request->qr_code)
                ->whereDoesntHave('parkingExit')
                ->first();

            if (!$parkingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode parkir tidak valid atau pengguna sudah keluar'
                ], 400);
            }

            $transactionService = app(\App\Services\ParkingTransactionService::class);

            // Ambil user terkait untuk menentukan biaya parkir
            $userTarget = $parkingEntry->user;
            $baseParkingFee = 1000; // Biaya dasar Rp 1000
            $calculatedParkingFee = $transactionService->calculateConditionalFee($userTarget->id, $baseParkingFee);

            // Jika belum dibayar, proses pembayaran
            if (!$transactionService->isPaid($parkingEntry->id)) {
                // Buat transaksi pembayaran secara otomatis
                $transactionService->createPaymentTransaction(
                    $parkingEntry->id,
                    $calculatedParkingFee,
                    'cash',
                    [
                        'paid_amount' => $calculatedParkingFee,
                        'change' => 0,
                        'note' => 'Diproses oleh admin/petugas'
                    ]
                );
            }

            // Buat catatan keluar
            $parkingExit = \App\Models\ParkingExit::create([
                'user_id' => $parkingEntry->user_id,
                'parking_entry_id' => $parkingEntry->id,
                'exit_time' => Carbon::now(),
                'exit_location' => $request->exit_location ?? null,
                'parking_fee' => $calculatedParkingFee,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan keluar berhasil direkam',
                'exit' => $parkingExit,
                'kode_parkir' => $parkingEntry->kode_parkir,
                'user_name' => $parkingEntry->user->name,
                'parking_fee' => $calculatedParkingFee,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in adminUpdateExit: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data keluar oleh admin/petugas'
            ], 500);
        }
    }

    /**
     * Proses scan barcode oleh admin/petugas untuk memindai QR code milik pengguna
     * Fungsi ini menangani baik masuk (entry) dan keluar (exit)
     * Jika kode yang dipindai adalah kode parkir (bukan QR code pengguna), maka proses sebagai exit
     * Jika kode yang dipindai adalah QR code pengguna, maka proses sebagai entry
     */
    public function adminScanBarcode(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $user = Auth::user();

            // Coba validasi sebagai QR code terlebih dahulu
            $qrCodeModel = $this->qrCodeService->validateQRCodeForEntry($request->qr_code);

            if ($qrCodeModel) {
                // Ini adalah QR code valid, cek apakah milik pengguna atau umum
                if ($qrCodeModel->user_id) {
                    // Ini adalah QR code milik pengguna, proses sebagai entry
                    $qrCodeOwner = \App\Models\User::find($qrCodeModel->user_id);
                    if (!$qrCodeOwner) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Pemilik QR code tidak ditemukan'
                        ], 400);
                    }

                    // Cek apakah sudah ada entri aktif untuk pengguna ini
                    $activeEntry = \App\Models\ParkingEntry::where('user_id', $qrCodeModel->user_id)
                        ->whereDoesntHave('parkingExit')
                        ->first();

                    if ($activeEntry) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Pengguna sudah memiliki catatan masuk aktif'
                        ], 400);
                    }

                    // Buat entri baru untuk pemilik QR code
                    $parkingEntry = \App\Models\ParkingEntry::create([
                        'user_id' => $qrCodeModel->user_id,
                        'qr_code_id' => $qrCodeModel->id,
                        'entry_time' => Carbon::now(),
                        'entry_location' => $request->entry_location ?? null,
                        'vehicle_type' => $qrCodeOwner->vehicle_type,
                        'vehicle_plate_number' => $qrCodeOwner->vehicle_plate_number,
                    ]);

                    // Generate kode parkir
                    $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkirFromEntry($parkingEntry);

                    // Perbarui kode parkir
                    $parkingEntry->update([
                        'kode_parkir' => $kodeParkir
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Catatan masuk berhasil direkam',
                        'entry' => $parkingEntry,
                        'user_name' => $qrCodeOwner->name,
                        'kode_parkir' => $kodeParkir,
                    ]);
                } else {
                    // Ini adalah QR code umum, proses sebagai entry untuk pengguna saat ini (admin/petugas)
                    $activeEntry = \App\Models\ParkingEntry::where('user_id', $user->id)
                        ->whereDoesntHave('parkingExit')
                        ->first();

                    if ($activeEntry) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda masih memiliki catatan masuk aktif'
                        ], 400);
                    }

                    $parkingEntry = \App\Models\ParkingEntry::create([
                        'user_id' => $user->id,
                        'qr_code_id' => $qrCodeModel->id,
                        'entry_time' => Carbon::now(),
                        'entry_location' => $request->entry_location ?? null,
                        'vehicle_type' => $user->vehicle_type,
                        'vehicle_plate_number' => $user->vehicle_plate_number,
                    ]);

                    $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkirFromEntry($parkingEntry);

                    $parkingEntry->update([
                        'kode_parkir' => $kodeParkir
                    ]);

                    // Tandai QR code umum sebagai digunakan
                    $this->qrCodeService->markQRCodeAsUsed($qrCodeModel);

                    return response()->json([
                        'success' => true,
                        'message' => 'Catatan masuk berhasil direkam',
                        'entry' => $parkingEntry,
                        'user_name' => $user->name,
                        'kode_parkir' => $kodeParkir,
                    ]);
                }
            } else {
                // Bukan QR code valid, mungkin kode parkir - proses sebagai exit
                $parkingEntry = \App\Models\ParkingEntry::where('kode_parkir', $request->qr_code)
                    ->whereDoesntHave('parkingExit')
                    ->first();

                if ($parkingEntry) {
                    // Jika ditemukan entri dengan kode parkir ini, proses sebagai exit
                    $transactionService = app(\App\Services\ParkingTransactionService::class);

                    // Ambil user terkait untuk menentukan biaya parkir
                    $userTarget = $parkingEntry->user;
                    $baseParkingFee = 1000; // Biaya dasar Rp 1000
                    $calculatedParkingFee = $transactionService->calculateConditionalFee($userTarget->id, $baseParkingFee);

                    // Jika belum dibayar, proses pembayaran
                    if (!$transactionService->isPaid($parkingEntry->id)) {
                        // Buat transaksi pembayaran secara otomatis
                        $transactionService->createPaymentTransaction(
                            $parkingEntry->id,
                            $calculatedParkingFee,
                            'cash',
                            [
                                'paid_amount' => $calculatedParkingFee,
                                'change' => 0,
                                'note' => 'Diproses oleh admin/petugas'
                            ]
                        );
                    }

                    // Buat catatan keluar
                    $parkingExit = \App\Models\ParkingExit::create([
                        'user_id' => $parkingEntry->user_id,
                        'parking_entry_id' => $parkingEntry->id,
                        'exit_time' => Carbon::now(),
                        'exit_location' => $request->exit_location ?? null,
                        'parking_fee' => $calculatedParkingFee,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Catatan keluar berhasil direkam',
                        'exit' => $parkingExit,
                        'kode_parkir' => $parkingEntry->kode_parkir,
                        'user_name' => $parkingEntry->user->name,
                        'parking_fee' => $calculatedParkingFee,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR code atau kode parkir tidak valid'
                    ], 400);
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error in adminScanBarcode: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses barcode oleh admin/petugas'
            ], 500);
        }
    }

    /**
     * Tampilkan riwayat parkir lengkap untuk pengguna biasa (hanya data milik mereka sendiri)
     */
    public function userParkingHistory()
    {
        $user = Auth::user();

        // Ambil entri parkir milik pengguna yang sedang login
        $parkingEntries = ParkingEntry::with(['qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])
            ->where('user_id', $user->id)
            ->orderBy('entry_time', 'desc')
            ->paginate(15);

        // Ambil statistik untuk pengguna
        $totalEntries = ParkingEntry::where('user_id', $user->id)->count();
        $activeEntries = ParkingEntry::where('user_id', $user->id)
            ->whereDoesntHave('parkingExit')
            ->count();
        $totalExits = ParkingEntry::where('user_id', $user->id)
            ->has('parkingExit')
            ->count();
        $totalSpent = ParkingExit::join('parking_entries', 'parking_exits.parking_entry_id', '=', 'parking_entries.id')
            ->where('parking_entries.user_id', $user->id)
            ->sum('parking_fee');

        return view('parking.user-history', compact(
            'parkingEntries',
            'totalEntries',
            'activeEntries',
            'totalExits',
            'totalSpent'
        ));
    }

    /**
     * Tampilkan detail entri parkir dan barcode-nya
     */
    public function viewParkingDetail($id)
    {
        $user = Auth::user();

        // Ambil entri parkir milik pengguna yang sedang login
        $parkingEntry = ParkingEntry::with(['qrCode', 'parkingExit', 'user:id,name,username'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        // Generate QR code image dari QR code yang terkait
        $qrCodeImage = '';
        if ($parkingEntry->qrCode) {
            $qrCodeService = app(\App\Services\QRCodeService::class);
            $qrCodeImage = $qrCodeService->generateQRCodeImage($parkingEntry->qrCode->code);
        }

        return view('parking.detail', compact('parkingEntry', 'qrCodeImage'));
    }
}