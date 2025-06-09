<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AbsensiImport;
use App\Models\Pegawai;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    public function showImportForm()
    {
        return view('absensi.import');
    }

    // Proses import file Excel absensi
    public function import(Request $request)
    {
        $request->validate([
            'file_absensi' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new AbsensiImport, $request->file('file_absensi'));

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diimport');
    }

    // Tampilkan list absensi (setelah import berhasil dan juga bisa diakses manual)
    public function index()
    {
        $absensis = Absensi::with('pegawai')->paginate(20);
        return view('absensi.index', compact('absensis'));
    }

    // Fungsi untuk menampilkan rekap potongan gaji (bisa dikembangkan)
    public function rekapPotongan(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));

        $rekap = Pegawai::withSum(['absensis as total_potongan' => function ($query) use ($bulan, $tahun) {
            $query->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }], 'potongan_persen')->get()->map(function ($pegawai) {
            $pegawai->total_potongan = round($pegawai->total_potongan, 2);

            $potongan_pajak = $pegawai->gaji_pokok * 0.05; // 5%
            $potongan_bpjs = $pegawai->gaji_pokok * 0.01;  // 1%

            $insentif_bersih = $pegawai->insentif_kotor - ($pegawai->insentif_kotor * $pegawai->total_potongan / 100);

            $pegawai->gaji_total = ($pegawai->gaji_pokok - $potongan_pajak - $potongan_bpjs) + $insentif_bersih;

            $pegawai->potongan_pajak = $potongan_pajak;
            $pegawai->potongan_bpjs = $potongan_bpjs;
            $pegawai->insentif_bersih = $insentif_bersih;

            return $pegawai;
        });

        return view('rekap.gaji', compact('rekap', 'bulan', 'tahun'));
    }
}
