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

/*
|--------------------------------------------------------------------------
| 0) ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| 1) AUTENTIKASI (PUBLIK)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| 2) PENGADUAN PUBLIK
|--------------------------------------------------------------------------
| Alur QR dan manual memakai URL serta handler yang berbeda.
| QR     : fasilitas ditentukan oleh token QR.
| Manual : pelapor memilih fasilitas dari formulir.
*/
Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');

Route::prefix('lapor')->name('pengaduan.')->group(function () {
    Route::get('/qr/{qr_code}', [PengaduanController::class, 'createQr'])
        ->name('qr.create');
    Route::post('/qr/{qr_code}', [PengaduanController::class, 'storeQr'])
        ->name('qr.store');

    Route::get('/manual', [PengaduanController::class, 'createManual'])
        ->name('manual.create');
    Route::post('/manual', [PengaduanController::class, 'storeManual'])
        ->name('manual.store');

    Route::get('/sukses/{pengaduan}', [PengaduanController::class, 'success'])
        ->name('success');

    // Kompatibilitas QR lama yang masih menyimpan URL /lapor/{qr_code}.
    Route::get('/{qr_code}', [PengaduanController::class, 'redirectLegacyQr'])
        ->name('qr.legacy');
});

/*
|--------------------------------------------------------------------------
| 3) AREA WAJIB LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/laporan', [DashboardController::class, 'laporan'])
        ->middleware('role:koordinator_lab')
        ->name('laporan.index');

    Route::get('/penugasan', [DashboardController::class, 'penugasan'])
        ->middleware('role:koordinator_lab')
        ->name('penugasan.index');

    Route::get('/detail-laporan', [DashboardController::class, 'detailLaporan'])
        ->middleware('role:koordinator_lab')
        ->name('detail-laporan.index');

    Route::get('/dashboard/pengaduan/{pengaduan}/detail', [DashboardController::class, 'detailPengaduan'])
        ->middleware('role:koordinator_lab')
        ->name('dashboard.pengaduan.detail');

    Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])
        ->middleware('role:koordinator_lab')
        ->name('tindak-lanjut.assign');

    Route::post('/notifikasi/{notifikasi}/kirim-ulang', [TindakLanjutController::class, 'kirimUlang'])
        ->middleware('role:koordinator_lab')
        ->name('notifikasi.kirim-ulang');

    Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])
        ->middleware('role:asisten')
        ->name('tindak-lanjut.update');

    Route::get('/fasilitas', [FasilitasController::class, 'index'])
        ->middleware('role:admin')
        ->name('fasilitas.index');
    Route::post('/fasilitas', [FasilitasController::class, 'store'])
        ->middleware('role:admin')
        ->name('fasilitas.store');
    Route::post('/fasilitas/{fasilitas}/regenerate-qr', [FasilitasController::class, 'regenerateQr'])
        ->middleware('role:admin')
        ->name('fasilitas.regenerate-qr');
        Route::middleware(['auth'])->group(function () {
            // Pastikan baris ini ada dan memiliki ->name('pengaduan')
            Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan');
            
            // Jika Anda ingin membuat pengaduan baru (form create), tambahkan juga:
            Route::get('/pengaduan/create', [PengaduanController::class, 'create'])->name('pengaduan.create');
            Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
        });

    Route::get('/laboratorium', [LaboratoriumController::class, 'index'])
        ->middleware('role:admin')
        ->name('laboratorium.index');
    Route::post('/laboratorium', [LaboratoriumController::class, 'store'])
        ->middleware('role:admin')
        ->name('laboratorium.store');
    Route::patch('/laboratorium/{laboratorium}', [LaboratoriumController::class, 'update'])
        ->middleware('role:admin')
        ->name('laboratorium.update');

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
