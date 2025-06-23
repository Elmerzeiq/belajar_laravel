<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji; // Pastikan model Gaji sudah ada
use App\Models\Pegawai;

// Tambahkan PDF facade
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

        $tahunList = range(date('Y') - 5, date('Y') + 5);

        $laporan = [];

        $filterBulan = $request->get('bulan');
        $filterTahun = $request->get('tahun');

        if ($filterBulan && $filterTahun) {
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

    // Method print: generate PDF seperti payroll_pdf
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
            $laporan = Gaji::with('pegawai')
                ->where('bulan', $filterBulan)
                ->where('tahun', $filterTahun)
                ->get();
        }

        // Generate PDF
        $pdf = Pdf::loadView('gaji.laporan_print', [
            'bulanList' => $bulanList,
            'laporan' => $laporan,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_gaji_'.$filterBulan.'_'.$filterTahun.'.pdf');
    }
}
