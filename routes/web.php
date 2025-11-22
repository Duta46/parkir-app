<?php

use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParkingManagementController;
use App\Http\Controllers\ParkingTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParkingController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [ParkingController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Parking QR code routes
    Route::get('/my-qr-code', [ParkingController::class, 'showQRCode'])->name('qr-code.show');
    Route::post('/generate-qr-code', [ParkingController::class, 'generateQRCode'])->name('qr-code.generate');
    Route::post('/scan-entry', [ParkingController::class, 'scanEntry'])->name('parking.scan.entry');
    Route::post('/scan-exit', [ParkingController::class, 'scanExit'])->name('parking.scan.exit');

    // Parking management routes for admin
    Route::prefix('parking-management')->middleware('role:Admin')->group(function() {
        Route::get('/', [ParkingManagementController::class, 'index'])->name('parking.management.index');
        Route::get('/all', [ParkingManagementController::class, 'all'])->name('parking.management.all');
        Route::get('/search', [ParkingManagementController::class, 'search'])->name('parking.management.search');
        Route::get('/{id}', [ParkingManagementController::class, 'show'])->name('parking.management.show');

        // CRUD routes
        Route::get('/create', [ParkingManagementController::class, 'create'])->name('parking.management.create');
        Route::post('/', [ParkingManagementController::class, 'store'])->name('parking.management.store');
        Route::get('/{id}/edit', [ParkingManagementController::class, 'edit'])->name('parking.management.edit');
        Route::put('/{id}', [ParkingManagementController::class, 'update'])->name('parking.management.update');
        Route::post('/{id}/exit', [ParkingManagementController::class, 'addExit'])->name('parking.management.addExit');
        Route::delete('/{id}', [ParkingManagementController::class, 'destroy'])->name('parking.management.destroy');
    });

    // Parking transaction routes for admin
    Route::prefix('parking-transactions')->middleware('role:Admin')->group(function() {
        Route::get('/', [ParkingTransactionController::class, 'index'])->name('parking.transactions.index');
        Route::get('/{id}', [ParkingTransactionController::class, 'show'])->name('parking.transactions.show');
        Route::get('/{id}/payment', [ParkingTransactionController::class, 'showPaymentForm'])->name('parking.transactions.payment.form');
        Route::post('/{id}/process-cash', [ParkingTransactionController::class, 'processCashPayment'])->name('parking.transactions.process-cash');
    });
});

// Auth routes
Route::get('/login', [App\Http\Controllers\CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\CustomLoginController::class, 'login'])->name('login.custom');
Route::post('/logout', [App\Http\Controllers\CustomLoginController::class, 'logout'])->name('logout');

// Other auth routes can remain if needed
// require __DIR__.'/auth.php';
