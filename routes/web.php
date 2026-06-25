<?php

use App\Http\Controllers\Auth\{LoginController, ForgotPasswordController};
use App\Http\Controllers\{DashboardController, PengaduanController, TindakLanjutController, RiwayatController, TeknisiController, ProfileController, ScanController};
use Illuminate\Support\Facades\Route;

// 1. Rute Publik
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.attempt');
});

// Password Reset Routes
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showEmailForm')->name('password.request');
    Route::post('/forgot-password', 'sendOtp')->name('password.email');
    Route::get('/forgot-password/otp', 'showOtpForm')->name('password.otp.form');
    Route::post('/forgot-password/otp', 'verifyOtp')->name('password.otp.verify');
    Route::post('/forgot-password/otp/resend', 'resendOtp')->name('password.otp.resend');
    Route::get('/reset-password', 'showResetForm')->name('password.reset.form');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});

Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');

// 2. Rute Terlindungi (Auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaduan
    Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::get('/', [PengaduanController::class, 'index'])->name('index');
        Route::get('/create', [PengaduanController::class, 'create'])->name('create');
        Route::post('/', [PengaduanController::class, 'store'])->name('store');
    });

    // Tindak Lanjut
    Route::prefix('tindak-lanjut')->name('tindak-lanjut.')->group(function () {
        Route::get('/', [TindakLanjutController::class, 'index'])->name('index');
        Route::post('/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])
            ->middleware('role:koordinator_lab')->name('assign');
        Route::patch('/{tindakLanjut}', [TindakLanjutController::class, 'update'])
            ->middleware('role:asisten')->name('update');
    });
    
    // Riwayat & Teknisi
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});

