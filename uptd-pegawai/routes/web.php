<?php

use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\GajiReviewController;
use App\Http\Controllers\PotonganTetapController;
use App\Http\Controllers\AbsenImportController;
use App\Http\Controllers\PayrollController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Resource route untuk CRUD pegawai
    Route::resource('pegawai', PegawaiController::class);

    // Route untuk rekap potongan gaji
    Route::get('rekap/gaji', [AbsensiController::class, 'rekapPotongan'])->name('rekap.gaji');

    // Halaman utama setelah login
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Resource route untuk CRUD potongan tetap
    Route::resource('potongan-tetap', PotonganTetapController::class);

    // gaji index
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');

    // Import Excel → Preview
    Route::post('/absen/import', [AbsenImportController::class, 'import'])->name('absen.import');
    Route::get('/gaji/preview/{pegawai_id}/{bulan}/{tahun}', [AbsenImportController::class, 'preview'])->name('gaji.preview');

    // Preview → Review (POST dari preview ke review)
   //Route::post('/gaji/review', [GajiReviewController::class, 'review'])->name('gaji.review');
   // Route::any('/gaji/review', [GajiReviewController::class, 'review'])->name('gaji.review');
    Route::match(['get', 'post'], '/gaji/review', [GajiReviewController::class, 'review'])->name('gaji.review');


    // (Opsional) Import Absensi, jika ada tombol import absensi manual
    Route::post('gaji/import-absensi', [GajiReviewController::class, 'importAbsensi'])->name('gaji.importAbsensi');

    // Simpan Gaji
    Route::post('/gaji/simpan', [GajiReviewController::class, 'simpan'])->name('gaji.simpan');

    // Hapus Gaji
    Route::delete('gaji', [GajiReviewController::class, 'hapus'])->name('gaji.hapus');

    //payroll pdf
    Route::get('/gaji/{id}/slip-pdf', [PayrollController::class, 'slip'])->name('gaji.slipPdf');

    // Untuk tombol Print per pegawai, bulan, tahun
Route::get('/gaji/{pegawai_id}/{bulan}/{tahun}/payroll_pdf', [PayrollController::class, 'payrollPdf'])->name('gaji.payroll_pdf');
});
