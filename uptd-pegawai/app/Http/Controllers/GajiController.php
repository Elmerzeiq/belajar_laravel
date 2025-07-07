<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Gaji;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $pegawaiList = Pegawai::where('status', 'aktif')->get();
        // Urutan tahun dari sekarang ke 5 tahun ke belakang
        $tahunList = range(date('Y'), date('Y') - 5);

        $selectedPegawai = null;
        $selectedTahun = null;
        $bulanList = [];

        if ($request->filled(['pegawai_id', 'tahun'])) {
            $selectedPegawai = Pegawai::find($request->pegawai_id);
            $selectedTahun = $request->tahun;

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];

            for ($i = 1; $i <= 12; $i++) {
                $gaji = Gaji::where('pegawai_id', $selectedPegawai->id)
                    ->where('tahun', $selectedTahun)
                    ->where('bulan', $i)
                    ->first();

                $bulanList[] = [
                    'nomor' => $i, // nomor bulan (untuk dipakai di link Import Excel)
                    'nama' => $namaBulan[$i],
                    'is_imported' => $gaji ? true : false,
                    'gaji_total' => $gaji->gaji_bersih ?? null, // sesuaikan dengan kolom gaji bersih di tabelmu
                ];
            }
        }

        return view('gaji.index', compact(
            'pegawaiList', 'tahunList',
            'selectedPegawai', 'selectedTahun', 'bulanList'
        ))->with('showTable', $request->filled(['pegawai_id', 'tahun']));
    }
}
