<?php

use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParkingManagementController;
use App\Http\Controllers\ParkingTransactionController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing-page');

Route::get('/dashboard', [ParkingController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Parking QR code routes - admin/petugas only for generation, all users for scanning
    Route::middleware('role:Admin|Petugas')->group(function() {
        Route::get('/my-qr-code', [ParkingController::class, 'showQRCode'])->name('qr-code.show');
        Route::post('/generate-qr-code', [ParkingController::class, 'generateQRCode'])->name('qr-code.generate');
    });

    Route::post('/scan-entry', [ParkingController::class, 'scanEntry'])->name('parking.scan.entry');
    Route::post('/scan-exit', [ParkingController::class, 'scanExit'])->name('parking.scan.exit');

    // Scan barcode route for users (Pengguna role or Dosen/Mahasiswa user_type)
    Route::get('/scan-barcode', [ParkingController::class, 'showScanPage'])->middleware('canAccessScanPage')->name('scan.barcode.page');
    Route::post('/scan-barcode', [ParkingController::class, 'scanBarcode'])->middleware('canAccessScanPage')->name('scan.barcode');

    // Admin/Petugas scan barcode route
    Route::middleware('role:Admin|Petugas')->group(function() {
        Route::get('/admin-scan-barcode', [ParkingController::class, 'showAdminScanPage'])->name('admin.scan.barcode.page');
        Route::post('/admin-scan-barcode', [ParkingController::class, 'adminScanBarcode'])->name('admin.scan.barcode');
    });

    // Parking management routes for admin
    // Parking menu for users - view their complete parking data
    Route::get('/parking-history', [ParkingController::class, 'userParkingHistory'])->middleware(['auth', 'canAccessScanPage'])->name('parking.history');
    Route::get('/parking-history/{id}', [ParkingController::class, 'viewParkingDetail'])->middleware(['auth', 'canAccessScanPage'])->name('parking.history.detail');

    Route::prefix('parking-management')->middleware('role:Admin')->group(function() {
        Route::get('/', [ParkingManagementController::class, 'index'])->name('parking.management.index');
        Route::get('/all', [ParkingManagementController::class, 'all'])->name('parking.management.all');
        Route::get('/search', [ParkingManagementController::class, 'search'])->name('parking.management.search');
        Route::get('/{id}', [ParkingManagementController::class, 'show'])->name('parking.management.show');

        // CRUD routes
        Route::get('/create', [ParkingManagementController::class, 'create'])->name('parking.management.create');
        Route::post('/', [ParkingManagementController::class, 'store'])->name('parking.management.store');
        Route::post('/generate-qr-umum', [ParkingManagementController::class, 'generateQRCodeUmum'])->name('parking.management.generate-qr-umum');
        Route::post('/generate-qr-user', [ParkingManagementController::class, 'generateQRCodeForUser'])->name('parking.management.generate-qr-user');
        Route::get('/{id}/edit', [ParkingManagementController::class, 'edit'])->name('parking.management.edit');
        Route::put('/{id}', [ParkingManagementController::class, 'update'])->name('parking.management.update');
        Route::post('/{id}/exit', [ParkingManagementController::class, 'addExit'])->name('parking.management.addExit');
        Route::post('/{id}/process-exit', [ParkingManagementController::class, 'processExit'])->name('parking.management.process-exit');
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

Route::get('/register', [App\Http\Controllers\CustomRegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\CustomRegisterController::class, 'register'])->name('register.store');

// Password reset routes (using Laravel's built-in controllers)
Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');

// Users management routes
Route::middleware(['auth', 'role:Admin'])->prefix('users')->group(function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});
