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

            // Set biaya parkir tetap Rp 2000
            $parkingFee = 2000;

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
                'user_name' => $parkingEntry->user->name
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