<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Jumlah semua pegawai (karena jika tidak aktif akan dihapus)
        $jumlahPegawai = Pegawai::count();

        // Jumlah pegawai yang belum diinput gajinya bulan ini
        $bulan = date('m');
        $tahun = date('Y');
        $jumlahBelumGaji = Pegawai::whereDoesntHave('gaji', function($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            })
            ->count();

        return view('home', [
            'jumlahPegawai' => $jumlahPegawai,
            'jumlahBelumGaji' => $jumlahBelumGaji,
        ]);
    }
}
