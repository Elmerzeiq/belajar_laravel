<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Pegawai;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanGajiController extends Controller
{
    public function index(Request $request)
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Tahun fleksibel: 1900 - 2100
        $tahunList = array_reverse(range(1900, 2100));

        $laporan = [];
        $filterBulan = $request->get('bulan');
        $filterTahun = $request->get('tahun');

        if ($filterBulan && $filterTahun) {
            $request->validate([
                'bulan' => 'required|numeric|min:1|max:12',
                'tahun' => 'required|numeric|min:1900|max:2100',
            ]);

            $laporan = Gaji::with('pegawai')
                ->where('bulan', $filterBulan)
                ->where('tahun', $filterTahun)
                ->get();
        }

        return view('gaji.laporan', [
            'bulanList' => $bulanList,
            'tahunList' => $tahunList,
            'laporan' => $laporan,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
        ]);
    }

    public function print(Request $request)
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $filterBulan = $request->get('bulan');
        $filterTahun = $request->get('tahun');

        $laporan = [];
        if ($filterBulan && $filterTahun) {
            $request->validate([
                'bulan' => 'required|numeric|min:1|max:12',
                'tahun' => 'required|numeric|min:1900|max:2100',
            ]);

            $laporan = Gaji::with('pegawai')
                ->where('bulan', $filterBulan)
                ->where('tahun', $filterTahun)
                ->get();
        }

        $pdf = Pdf::loadView('gaji.laporan_print', [
            'bulanList' => $bulanList,
            'laporan' => $laporan,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_gaji_' . $filterBulan . '_' . $filterTahun . '.pdf');
    }
}
