<?php

namespace App\Http\Controllers;

use App\Models\ParkingEntry;
use App\Models\ParkingTransaction;
use App\Services\ParkingTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingTransactionController extends Controller
{
    protected ParkingTransactionService $transactionService;

    public function __construct(ParkingTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman transaksi pembayaran
     */
    public function index()
    {
        // Hanya untuk admin
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $transactions = ParkingTransaction::with(['user', 'parkingEntry'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('parking.transactions.index', compact('transactions'));
    }

    /**
     * Tampilkan form pembayaran untuk entri tertentu
     */
    public function showPaymentForm($id)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $parkingEntry = ParkingEntry::with(['user', 'parkingExit'])->findOrFail($id);

        // Hitung biaya parkir jika belum keluar
        $expectedAmount = 0;
        if (!$parkingEntry->parkingExit) {
            // Jika belum keluar, hitung hingga saat ini
            $expectedAmount = $this->transactionService->calculateParkingFee(
                $parkingEntry->entry_time,
                now()
            );
        } elseif ($parkingEntry->parkingExit) {
            // Jika sudah keluar, gunakan biaya dari parking_exit
            $expectedAmount = $parkingEntry->parkingExit->parking_fee ?? 0;
        }

        return view('parking.transactions.payment', compact('parkingEntry', 'expectedAmount'));
    }

    /**
     * Proses pembayaran cash
     */
    public function processCashPayment(Request $request, $id)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $parkingEntry = ParkingEntry::findOrFail($id);

        // Hitung biaya parkir
        $expectedAmount = $this->transactionService->calculateParkingFee(
            $parkingEntry->entry_time,
            now()
        );

        // Proses pembayaran cash
        $result = $this->transactionService->processCashPayment(
            $id,
            $request->paid_amount,
            $expectedAmount
        );

        if ($result['success']) {
            return redirect()->route('parking.transactions.index')
                ->with('success', $result['message'] . (isset($result['change']) ? ' Kembalian: Rp' . number_format($result['change'], 0, ',', '.') : ''));
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->with('required_amount', $result['required_amount'] ?? 0)
                ->with('paid_amount', $request['paid_amount']);
        }
    }

    /**
     * Tampilkan detail transaksi
     */
    public function show($id)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $transaction = ParkingTransaction::with(['user', 'parkingEntry'])->findOrFail($id);

        return view('parking.transactions.show', compact('transaction'));
    }
}
