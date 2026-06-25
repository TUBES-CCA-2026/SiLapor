<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\TindakLanjutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

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
Route::prefix('lapor')->name('pengaduan.')->group(function () {
    Route::get('/qr/{qr_code}', [PengaduanController::class, 'createQr'])->name('qr.create');
    Route::post('/qr/{qr_code}', [PengaduanController::class, 'storeQr'])->name('qr.store');

    Route::get('/manual', [PengaduanController::class, 'createManual'])->name('manual.create');
    Route::post('/manual', [PengaduanController::class, 'storeManual'])->name('manual.store');

    Route::get('/sukses/{pengaduan}', [PengaduanController::class, 'success'])->name('success');
    Route::get('/{qr_code}', [PengaduanController::class, 'redirectLegacyQr'])->name('qr.legacy');
});

/*
|--------------------------------------------------------------------------
| Area Wajib Login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pengaduan-foto/{foto}', [PengaduanController::class, 'showFoto'])->name('pengaduan-foto.show');

    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');

    Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])
        ->middleware('role:asisten')
        ->name('tindak-lanjut.update');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:koordinator_lab')->group(function () {
        Route::get('/laporan', [DashboardController::class, 'laporan'])->name('laporan.index');
        Route::get('/penugasan', [DashboardController::class, 'penugasan'])->name('penugasan.index');
        Route::get('/detail-laporan', [DashboardController::class, 'detailLaporan'])->name('detail-laporan.index');
        Route::get('/dashboard/pengaduan/{pengaduan}/detail', [DashboardController::class, 'detailPengaduan'])->name('dashboard.pengaduan.detail');
        Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])->name('tindak-lanjut.assign');
        Route::post('/notifikasi/{notifikasi}/kirim-ulang', [TindakLanjutController::class, 'kirimUlang'])->name('notifikasi.kirim-ulang');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas.index');
        Route::post('/fasilitas', [FasilitasController::class, 'store'])->name('fasilitas.store');
        Route::post('/fasilitas/{fasilitas}/regenerate-qr', [FasilitasController::class, 'regenerateQr'])->name('fasilitas.regenerate-qr');

        Route::get('/laboratorium', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
        Route::post('/laboratorium', [LaboratoriumController::class, 'store'])->name('laboratorium.store');
        Route::patch('/laboratorium/{laboratorium}', [LaboratoriumController::class, 'update'])->name('laboratorium.update');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
    });
});
