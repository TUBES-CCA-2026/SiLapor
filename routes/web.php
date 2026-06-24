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
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. Rute Publik
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

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
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');

    // Tindak Lanjut
    Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])->middleware('role:koordinator_lab')->name('tindak-lanjut.assign');
    Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->middleware('role:asisten')->name('tindak-lanjut.update');
    
    // Riwayat & Teknisi
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});