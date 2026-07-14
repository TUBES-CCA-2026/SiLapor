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
use App\Http\Controllers\TindakLanjutController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::get('/contact-laboratory-support', function () {
    $laborans = User::with(['roleData', 'profile'])
        ->role('laboran')
        ->orderBy('nama')
        ->get();

    return view('support.contact', compact('laborans'));
})->name('support.contact');

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
    Route::get('/api/fasilitas', [PengaduanController::class, 'apiFasilitas'])->name('manual.api.fasilitas');
    Route::get('/sukses/{pengaduan}', [PengaduanController::class, 'success'])->name('success');
    Route::get('/{qr_code}', [PengaduanController::class, 'redirectLegacyQr'])->name('qr.legacy');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengaduan-foto/{foto}', [PengaduanController::class, 'showFoto'])->name('pengaduan-foto.show');
    Route::get('/users/{user}/foto', [ProfileController::class, 'showPhoto'])->name('users.photo');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    Route::middleware('role:asisten')->group(function () {
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::get('/', [PengaduanController::class, 'index'])->name('index');
            Route::get('/create', [PengaduanController::class, 'create'])->name('create');
            Route::post('/', [PengaduanController::class, 'store'])->name('store');
            Route::get('/api/fasilitas', [PengaduanController::class, 'apiFasilitas'])->name('api.fasilitas');
        });

        Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
        Route::patch('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->name('tindak-lanjut.update');
    });

    Route::get('/riwayat', [RiwayatController::class, 'index'])
        ->middleware('role:asisten,laboran')
        ->name('riwayat.index');

    Route::middleware('role:koordinator_lab,laboran')->group(function () {
        Route::get('/laporan', [DashboardController::class, 'laporan'])->name('laporan.index');
    });

    Route::get('/dashboard/pengaduan/{pengaduan}/detail', [DashboardController::class, 'detailPengaduan'])
        ->middleware('role:asisten,koordinator_lab,laboran,kepala_lab')
        ->name('dashboard.pengaduan.detail');

    Route::patch('/laporan/{pengaduan}', [DashboardController::class, 'updatePengaduan'])
        ->middleware('role:koordinator_lab,laboran')
        ->name('laporan.update');

    Route::delete('/laporan/{pengaduan}', [DashboardController::class, 'destroyPengaduan'])
        ->middleware('role:koordinator_lab,laboran')
        ->name('laporan.destroy');

    Route::patch('/riwayat/{pengaduan}', [DashboardController::class, 'updatePengaduan'])
        ->middleware('role:asisten,laboran')
        ->name('riwayat.update');

    Route::delete('/riwayat/{pengaduan}', [DashboardController::class, 'destroyPengaduan'])
        ->middleware('role:asisten,laboran')
        ->name('riwayat.destroy');

    Route::get('/rekapsulasi/export/excel', [DashboardController::class, 'exportRekapsulasiExcel'])
        ->middleware('role:laboran')
        ->name('rekapsulasi.export.excel');

    Route::get('/rekapsulasi/export/pdf', [DashboardController::class, 'exportRekapsulasiPdf'])
        ->middleware('role:laboran')
        ->name('rekapsulasi.export.pdf');

    Route::get('/rekapsulasi/import-template', [DashboardController::class, 'importRekapsulasiTemplate'])
        ->middleware('role:laboran')
        ->name('rekapsulasi.import-template');

    Route::post('/rekapsulasi/import', [DashboardController::class, 'importRekapsulasi'])
        ->middleware('role:laboran')
        ->name('rekapsulasi.import');

    Route::middleware('role:koordinator_lab')->group(function () {
        Route::get('/detail-laporan', [DashboardController::class, 'detailLaporan'])->name('detail-laporan.index');
        Route::get('/penugasan', [DashboardController::class, 'penugasan'])->name('penugasan.index');
        Route::post('/pengaduan/{pengaduan}/assign', [TindakLanjutController::class, 'assign'])->name('tindak-lanjut.assign');
    });

    Route::middleware('role:laboran,kepala_lab')->group(function () {
        Route::get('/rekapsulasi', [DashboardController::class, 'rekapsulasi'])->name('rekapsulasi.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas.index');
        Route::post('/fasilitas', [FasilitasController::class, 'store'])->name('fasilitas.store');
        Route::post('/fasilitas/kategori', [FasilitasController::class, 'storeKategori'])->name('fasilitas.kategori.store');
        Route::get('/fasilitas/import-template', [FasilitasController::class, 'importTemplate'])->name('fasilitas.import-template');
        Route::post('/fasilitas/import', [FasilitasController::class, 'import'])->name('fasilitas.import');
        Route::post('/fasilitas/{fasilitas}/regenerate-qr', [FasilitasController::class, 'regenerateQr'])->name('fasilitas.regenerate-qr');
        Route::delete('/fasilitas/{fasilitas}/qr', [FasilitasController::class, 'deleteQr'])->name('fasilitas.delete-qr');

    });

    Route::middleware('role:laboran,admin')->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
            Route::get('/users/import-template', [AdminUserController::class, 'importTemplate'])->name('users.import-template');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
    });

    Route::middleware('role:laboran')->group(function () {
        Route::get('/laboratorium', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
        Route::post('/laboratorium', [LaboratoriumController::class, 'store'])->name('laboratorium.store');
        Route::patch('/laboratorium/{laboratorium}', [LaboratoriumController::class, 'update'])->name('laboratorium.update');
        Route::delete('/laboratorium/{laboratorium}', [LaboratoriumController::class, 'destroy'])->name('laboratorium.destroy');
    });
});
