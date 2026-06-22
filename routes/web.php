<?php
/**
 * ============================================================
 *  TAMBAHKAN BLOK INI KE routes/web.php KAMU YANG SUDAH ADA
 * ============================================================
 */

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
| 0) ROOT '/' -> langsung ke login (atau dashboard kalau sudah login)
|    Ini yang membuat `php artisan serve` langsung mengarah ke halaman login.
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| 1) LOGIN  (publik)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| 1b) FORGOT PASSWORD via OTP  (publik)
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| 2) SCAN QR + LAPOR  (PUBLIK / GUEST — sengaja TANPA middleware auth,
|    supaya bisa diakses tanpa login sesuai requirement)
|--------------------------------------------------------------------------
*/
Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');

// Hasil decode QR mengarah ke sini. Kalau user sudah login, sistem otomatis
// mengenali dia (lihat PengaduanController). Kalau belum, tetap bisa lanjut
// sebagai guest, ATAU pilih "Login dulu" (lihat tombol di view).
Route::get('/lapor/{qr_code}', [PengaduanController::class, 'create'])->name('scan.show');
Route::post('/lapor/{qr_code}', [PengaduanController::class, 'store'])->name('pengaduan.store');
Route::get('/lapor/sukses/{pengaduan}', [PengaduanController::class, 'success'])->name('pengaduan.success');

/*
|--------------------------------------------------------------------------
| 3) AREA YANG WAJIB LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Koordinator Lab menugaskan asisten -> trigger notifikasi email
    Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])
        ->middleware('role:koordinator_lab')
        ->name('tindak-lanjut.assign');

    // Kirim ulang notifikasi yang gagal (status_pengiriman = failed)
    Route::post('/notifikasi/{notifikasi}/kirim-ulang', [TindakLanjutController::class, 'kirimUlang'])
        ->middleware('role:koordinator_lab')
        ->name('notifikasi.kirim-ulang');

    // Asisten update progres perbaikan
    Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])
        ->middleware('role:asisten')
        ->name('tindak-lanjut.update');

    // Kelola fasilitas & cetak QR — KHUSUS admin (koordinator_lab & kepala_lab
    // sudah tidak punya akses bikin/regenerasi QR lagi)
    Route::get('/fasilitas', [FasilitasController::class, 'index'])
        ->middleware('role:admin')
        ->name('fasilitas.index');
    Route::post('/fasilitas', [FasilitasController::class, 'store'])
        ->middleware('role:admin')
        ->name('fasilitas.store');
    Route::post('/fasilitas/{fasilitas}/regenerate-qr', [FasilitasController::class, 'regenerateQr'])
        ->middleware('role:admin')
        ->name('fasilitas.regenerate-qr');

    // Kelola data laboratorium — KHUSUS admin
    Route::get('/laboratorium', [LaboratoriumController::class, 'index'])
        ->middleware('role:admin')
        ->name('laboratorium.index');
    Route::post('/laboratorium', [LaboratoriumController::class, 'store'])
        ->middleware('role:admin')
        ->name('laboratorium.store');
    Route::patch('/laboratorium/{laboratorium}', [LaboratoriumController::class, 'update'])
        ->middleware('role:admin')
        ->name('laboratorium.update');

    // Kelola user — KHUSUS admin (tambah akun, edit profil, reset password
    // langsung tanpa OTP, hapus user)
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
