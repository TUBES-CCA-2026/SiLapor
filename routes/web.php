<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TindakLanjutController;
use Illuminate\Support\Facades\Route;

// 1. Rute Publik (Tanpa Auth)
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');

// 2. Rute Prefix Lapor (Pastikan tidak bentrok)
// Tambahkan baris ini di dalam grup middleware('auth')
use App\Http\Controllers\RiwayatController;

Route::middleware('auth')->group(function () {
    // ... rute lainnya
    
    // Pastikan baris ini ada
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

// 3. Rute Terlindungi (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Pengaduan & Tindak Lanjut
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');

    Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    
    Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])->middleware('role:koordinator_lab')->name('tindak-lanjut.assign');
    Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->middleware('role:asisten')->name('tindak-lanjut.update');
    
    // ... (sisanya tetap sama)
});
