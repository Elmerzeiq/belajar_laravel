<?php
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/pegawai', function(){
    return view('pegawai');
});

Route::resource('pegawai', PegawaiController::class);
