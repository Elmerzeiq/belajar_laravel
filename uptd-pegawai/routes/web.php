<?php

use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\GajiReviewController;
use App\Http\Controllers\PotonganTetapController;
use App\Http\Controllers\AbsenImportController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LaporanGajiController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    // Halaman utama setelah login
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Profil user (semua role)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    /**
     * ================================
     * Routes untuk Pembantu Bendahara
     * ================================
     */
    Route::middleware(['role:Pembantu Bendahara'])->group(function () {
        Route::resource('pegawai', PegawaiController::class);
        Route::resource('potongan-tetap', PotonganTetapController::class);

        Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
        Route::post('/absen/import', [AbsenImportController::class, 'import'])->name('absen.import');
        Route::get('/gaji/preview/{pegawai_id}/{bulan}/{tahun}', [AbsenImportController::class, 'preview'])->name('gaji.preview');

        Route::post('/gaji/review', [GajiReviewController::class, 'review'])->name('gaji.review');
        Route::post('/gaji/import-absensi', [GajiReviewController::class, 'importAbsensi'])->name('gaji.importAbsensi');
        Route::post('/gaji/simpan', [GajiReviewController::class, 'simpan'])->name('gaji.simpan');
        Route::delete('gaji', [GajiReviewController::class, 'hapus'])->name('gaji.hapus');

        Route::get('/gaji/{id}/slip-pdf', [PayrollController::class, 'slip'])->name('gaji.slipPdf');
        Route::get('/gaji/{pegawai_id}/{bulan}/{tahun}/payroll_pdf', [PayrollController::class, 'payrollPdf'])->name('gaji.payroll_pdf');
    });

    /**
     * ===========================
     * Routes untuk Kepala Bendahara
     * ===========================
     */
    Route::middleware(['role:Bendahara Kepala'])->group(function () {
        Route::get('/laporan-gaji', [LaporanGajiController::class, 'index'])->name('laporan.gaji');
        Route::get('/laporan-gaji/print', [LaporanGajiController::class, 'print'])->name('laporan.gaji.print');
    });

    /**
     * ===========================
     * Routes Umum (Semua Role)
     * ===========================
     */
    Route::get('/gaji/review', [GajiReviewController::class, 'review'])->name('gaji.review');
});
