<?php

use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Resource route untuk CRUD pegawai
    Route::resource('pegawai', PegawaiController::class);

    // Route untuk import data absensi (form dan proses import)
    Route::get('absensi/import', [AbsensiController::class, 'showImportForm'])->name('absensi.import.form');
    Route::post('absensi/import', [AbsensiController::class, 'import'])->name('absensi.import');

    // Route untuk menampilkan data absensi
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');

    // Route untuk rekap potongan gaji
    Route::get('rekap/gaji', [AbsensiController::class, 'rekapPotongan'])->name('rekap.gaji');

    // Route halaman utama setelah login
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Resource route untuk CRUD potongan tetap
    Route::resource('potongan-tetap', App\Http\Controllers\PotonganTetapController::class);

});
