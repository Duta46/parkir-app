<?php

namespace App\Http\Controllers;

use App\Models\ParkingEntry;
use App\Models\ParkingExit;
use App\Models\QrCode;
use App\Models\User;
use App\Services\ParkingTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Authorize user is admin or petugas
     */
    private function authorizeAdmin()
    {
        if (!Auth::check() || (!Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Petugas'))) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    /**
     * Build search query with applied filters
     */
    private function buildSearchQuery(array $filters)
    {
        $query = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])
            ->orderBy('entry_time', 'desc');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['entry_date'])) {
            $query->whereDate('entry_time', $filters['entry_date']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereDoesntHave('parkingExit');
            } elseif ($filters['status'] === 'completed') {
                $query->whereHas('parkingExit');
            }
        }

        if (!empty($filters['vehicle_type'])) {
            $query->where('vehicle_type', $filters['vehicle_type']);
        }

        return $query;
    }

    /**
     * Tampilkan dashboard manajemen parkir untuk admin
     */
    public function index()
    {
        $this->authorizeAdmin();

        // Ambil data parkir terbaru dengan eager loading terbatas untuk mencegah N+1
        $parkingEntries = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])
            ->orderBy('entry_time', 'desc')
            ->paginate(15);

        // Ambil statistik dengan query yang dioptimalkan
        $totalUsers = User::count();
        $activeEntries = ParkingEntry::whereDoesntHave('parkingExit')->count();
        $totalExitsToday = ParkingExit::whereDate('exit_time', today())->count();
        $totalRevenueToday = ParkingExit::whereDate('exit_time', today())
            ->sum('parking_fee');

        // Statistik tambahan
        $today = today();
        $totalEntriesToday = ParkingEntry::whereDate('entry_time', $today)->count();
        $totalRevenueThisMonth = ParkingExit::whereMonth('exit_time', $today->month)
                                           ->whereYear('exit_time', $today->year)
                                           ->sum('parking_fee');
        $totalEntriesThisMonth = ParkingEntry::whereMonth('entry_time', $today->month)
                                            ->whereYear('entry_time', $today->year)
                                            ->count();

        $revenueByVehicleType = ParkingExit::join('parking_entries', 'parking_exits.parking_entry_id', '=', 'parking_entries.id')
                                          ->selectRaw('parking_entries.vehicle_type, COUNT(*) as count, SUM(parking_exits.parking_fee) as total_revenue')
                                          ->whereDate('parking_exits.exit_time', $today)
                                          ->groupBy('parking_entries.vehicle_type')
                                          ->get();

        // Data untuk grafik (7 hari terbaru) - dioptimalkan dengan satu query
        $statistics = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i); // Gunakan copy() agar tidak mengubah $today asli
            $entryCount = ParkingEntry::whereDate('entry_time', $date)->count();
            $exitCount = ParkingExit::whereDate('exit_time', $date)->count();
            $revenue = ParkingExit::whereDate('exit_time', $date)->sum('parking_fee');

            $statistics[] = [
                'date' => $date->format('d M'),
                'entries' => $entryCount,
                'exits' => $exitCount,
                'revenue' => $revenue
            ];
        }

        return view('parking.management.index', compact(
            'parkingEntries',
            'totalUsers',
            'activeEntries',
            'totalExitsToday',
            'totalRevenueToday',
            'totalEntriesToday',
            'totalRevenueThisMonth',
            'totalEntriesThisMonth',
            'revenueByVehicleType',
            'statistics'
        ));
    }

    /**
     * Tampilkan semua data parkir
     */
    public function all()
    {
        $this->authorizeAdmin();

        $parkingEntries = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])
            ->orderBy('entry_time', 'desc')
            ->paginate(50);

        return view('parking.management.all', compact('parkingEntries'));
    }

    /**
     * Tampilkan detail data parkir
     */
    public function show($id)
    {
        $this->authorizeAdmin();

        // Ambil entri parkir dengan user dan parkingExit
        $parkingEntry = ParkingEntry::with(['user:id,name,username', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])->findOrFail($id);
        
        // Ambil QR code milik user, bukan dari entri parkir (yang bisa saja salah terkait)
        // Kita ambil QR code yang valid untuk user yang sama dan untuk tanggal yang sama
        $qrCode = QrCode::where('user_id', $parkingEntry->user_id)
            ->whereDate('date', $parkingEntry->entry_time->toDateString())
            ->orderBy('created_at', 'desc') // Ambil QR code terbaru untuk tanggal ini
            ->first();

        return view('parking.management.show', compact('parkingEntry', 'qrCode'));
    }

    /**
     * Cari data parkir
     */
    public function search(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'entry_date' => 'nullable|date',
            'status' => 'nullable|in:active,completed',
            'vehicle_type' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-]+$/',
        ]);

        $query = $this->buildSearchQuery($validated);
        $parkingEntries = $query->paginate(15);

        // Ambil semua pengguna untuk filter
        $users = User::select('id', 'name', 'username')->get();

        return view('parking.management.search', compact('parkingEntries', 'users'));
    }

    /**
     * Generate QR code umum harian untuk semua pengguna (untuk admin/petugas cetak barcode umum)
     */
    public function generateQRCodeUmum()
    {
        $this->authorizeAdmin();

        $qrCodeService = app(\App\Services\QRCodeService::class);

        // Generate QR code umum harian
        $qrCodeModel = $qrCodeService->generateDailyQRCodeUmum();

        return response()->json([
            'success' => true,
            'qr_code' => $qrCodeModel->code,
            'expires_at' => $qrCodeModel->expires_at->format('Y-m-d H:i:s'),
            'message' => 'QR code umum berhasil dibuat untuk hari ini'
        ]);
    }

    /**
     * Generate QR code untuk pengguna tertentu (untuk admin/petugas cetak barcode)
     */
    public function generateQRCodeForUser(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $qrCodeService = app(\App\Services\QRCodeService::class);

        // Generate QR code harian untuk pengguna
        $qrCodeModel = $qrCodeService->generateDailyQRCode($user);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCodeModel->code,
            'expires_at' => $qrCodeModel->expires_at->format('Y-m-d H:i:s'),
            'message' => 'QR code berhasil dibuat untuk ' . $user->name
        ]);
    }

    /**
     * Tampilkan form untuk membuat entri parkir manual
     */
    public function create()
    {
        $this->authorizeAdmin();

        $users = User::select('id', 'name', 'username')->get();
        return view('parking.management.create', compact('users'));
    }

    /**
     * Simpan entri parkir manual
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'entry_time' => 'required|date',
            'vehicle_type' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-]+$/',
            'vehicle_plate_number' => 'nullable|string|max:50|regex:/^[A-Z0-9\s]+$/',
            'entry_location' => 'nullable|string|max:255',
        ]);

        // Buat QR code untuk entri manual
        $qrCodeService = app(\App\Services\QRCodeService::class);
        $user = User::find($validated['user_id']);
        $date = $validated['entry_time'] ? \Carbon\Carbon::parse($validated['entry_time'])->toDateString() : today();
        $dailyQr = $qrCodeService->generateDailyQRCode($user, $date);

        // Buat catatan masuk parkir sementara tanpa kode parkir
        $parkingEntry = ParkingEntry::create([
            'user_id' => $validated['user_id'],
            'qr_code_id' => $dailyQr->id,
            'entry_time' => $validated['entry_time'],
            'entry_location' => $validated['entry_location'],
            'vehicle_type' => $validated['vehicle_type'],
            'vehicle_plate_number' => $validated['vehicle_plate_number'],
        ]);

        // Generate kode parkir berdasarkan ID parking entry yang baru dibuat
        $kodeParkir = app(\App\Services\ParkingTransactionService::class)->generateKodeParkirFromEntry($parkingEntry);

        // Perbarui kode parkir dengan format baru
        $parkingEntry->update([
            'kode_parkir' => $kodeParkir
        ]);

        // Tandai QR code sebagai digunakan
        $qrCodeService->markQRCodeAsUsed($dailyQr);

        return redirect()->route('parking.management.index')->with('success', 'Entri parkir berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit entri parkir
     */
    public function edit($id)
    {
        $this->authorizeAdmin();

        $parkingEntry = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])->findOrFail($id);
        $users = User::select('id', 'name', 'username')->get();

        return view('parking.management.edit', compact('parkingEntry', 'users'));
    }

    /**
     * Update entri parkir
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'entry_time' => 'required|date',
            'vehicle_type' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-]+$/',
            'vehicle_plate_number' => 'nullable|string|max:50|regex:/^[A-Z0-9\s]+$/',
            'entry_location' => 'nullable|string|max:255',
        ]);

        $parkingEntry = ParkingEntry::findOrFail($id);
        $parkingEntry->update([
            'user_id' => $validated['user_id'],
            'entry_time' => $validated['entry_time'],
            'entry_location' => $validated['entry_location'],
            'vehicle_type' => $validated['vehicle_type'],
            'vehicle_plate_number' => $validated['vehicle_plate_number'],
        ]);

        return redirect()->route('parking.management.show', $parkingEntry->id)->with('success', 'Entri parkir berhasil diperbarui.');
    }

    /**
     * Tambahkan entri keluar manual
     */
    public function addExit(Request $request, $id)
    {
        $this->authorizeAdmin();

        $parkingEntry = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])->findOrFail($id);

        if ($parkingEntry->parkingExit) {
            return redirect()->back()->with('error', 'Entri ini sudah memiliki catatan keluar.');
        }

        $validated = $request->validate([
            'exit_time' => 'required|date',
            'exit_location' => 'nullable|string|max:255',
        ]);

        // Ambil user terkait untuk menentukan biaya parkir
        $user = $parkingEntry->user;

        // Gunakan transaksi service untuk menghitung biaya berdasarkan kebijakan: 1x bayar per hari untuk user non-admin/petugas
        $transactionService = app(ParkingTransactionService::class);
        $baseParkingFee = 1000; // Biaya dasar Rp 1000
        $calculatedParkingFee = $transactionService->calculateConditionalFee($user->id, $baseParkingFee);

        $parkingExit = ParkingExit::create([
            'user_id' => $parkingEntry->user_id,
            'parking_entry_id' => $parkingEntry->id,
            'exit_time' => $validated['exit_time'],
            'exit_location' => $validated['exit_location'],
            'parking_fee' => $calculatedParkingFee,
        ]);

        // Jika biaya parkir adalah 0 (karena kebijakan 1x bayar per hari), buat transaksi pembayaran gratis
        if ($calculatedParkingFee == 0) {
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
        }

        return redirect()->route('parking.management.show', $parkingEntry->id)->with('success', 'Catatan keluar berhasil ditambahkan.');
    }

    /**
     * Proses keluar parkir dari All Records
     */
    public function processExit(Request $request, $id)
    {
        $this->authorizeAdmin();

        $parkingEntry = ParkingEntry::with(['user:id,name,username'])->findOrFail($id);

        // Cek apakah sudah keluar
        if ($parkingEntry->parkingExit) {
            return redirect()->back()->with('error', 'Entri ini sudah memiliki catatan keluar.');
        }

        // Validasi input pembayaran
        $validated = $request->validate([
            'exit_time' => 'required|date',
            'exit_location' => 'nullable|string|max:255',
        ]);

        // Ambil user terkait untuk menentukan biaya parkir
        $user = $parkingEntry->user;

        // Gunakan transaksi service untuk menghitung biaya berdasarkan kebijakan: 1x bayar per hari untuk user non-admin/petugas
        $transactionService = app(ParkingTransactionService::class);
        $baseParkingFee = 1000; // Biaya dasar Rp 1000
        $calculatedParkingFee = $transactionService->calculateConditionalFee($user->id, $baseParkingFee);

        // Buat catatan keluar parkir
        $parkingExit = ParkingExit::create([
            'user_id' => $parkingEntry->user_id,
            'parking_entry_id' => $parkingEntry->id,
            'exit_time' => $validated['exit_time'],
            'exit_location' => $validated['exit_location'] ?? null,
            'parking_fee' => $calculatedParkingFee,
        ]);

        // Jika biaya parkir adalah 0 (karena kebijakan 1x bayar per hari), buat transaksi pembayaran gratis
        if ($calculatedParkingFee == 0) {
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
        }

        return redirect()->route('parking.management.all')->with('success', 'Proses keluar berhasil dilakukan.');
    }

    /**
     * Hapus entri parkir
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $parkingEntry = ParkingEntry::findOrFail($id);

        // Hapus entri keluar terlebih dahulu jika ada
        if ($parkingEntry->parkingExit) {
            $parkingEntry->parkingExit()->delete();
        }

        $parkingEntry->delete();

        return redirect()->route('parking.management.index')->with('success', 'Entri parkir berhasil dihapus.');
    }

    /**
     * Generate and download PDF with parking information and QR code
     */
    public function downloadPDF($id)
    {
        $this->authorizeAdmin();

        $parkingEntry = ParkingEntry::with(['user:id,name,username', 'qrCode:id,user_id,code,date', 'parkingExit:id,parking_entry_id,exit_time,parking_fee'])->findOrFail($id);

        // Generate QR code for the entry if it has a QR code
        $qrCodeData = null;
        if ($parkingEntry->qrCode) {
            $qrCodeData = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($parkingEntry->qrCode->code));
        }

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('parking.management.pdf-ticket', compact('parkingEntry', 'qrCodeData'));

        return $pdf->download('parkir-ticket-' . $parkingEntry->kode_parkir . '.pdf');
    }
}