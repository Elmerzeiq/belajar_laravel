<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AbsensiImport;
use App\Models\AbsensiImport as AbsensiImportModel; // ALIAS!
use App\Models\Pegawai;
use App\Models\PotonganTetap;
use App\Models\Gaji;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class GajiReviewController extends Controller
{

    public function importAbsensi(Request $request)
    {
        $request->validate([
            'file_absensi' => 'required|mimes:xlsx,xls',
            'pegawai_id' => 'required|exists:pegawais,id',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        // Bersihkan data import sebelumnya (optional: hanya untuk pegawai, bulan, tahun tersebut)
        AbsensiImportModel::where([
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->delete();

        // Import
        Excel::import(new AbsensiImport($request->pegawai_id, $request->bulan, $request->tahun), $request->file('file_absensi'));

        // Redirect ke review
        return redirect()->route('gaji.review', [
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun
        ]);
    }

    public function review(Request $request)
    {
        $pegawai = Pegawai::findOrFail($request->pegawai_id);
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $absensi = AbsensiImportModel::where([
            'pegawai_id' => $pegawai->id,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ])->get();

        $potongan_tetap = PotonganTetap::all();

        // Ambil total potongan insentif import dari session (hasil import absen)
        $total_potongan_insentif_import = session('total_potongan_persen', 0);

        $potongan_absensi = 0;
        $jumlah_terlambat = 0;
        foreach ($absensi as $row) {
            if (!empty($row->terlambat)) {
                $jumlah_terlambat++;
                $potongan_absensi += 10000;
            }
        }

        $total_awal = ($pegawai->gaji_pokok ?? 0) + ($pegawai->insentif_kotor ?? 0);

        return view('gaji.review', [
            'pegawai' => $pegawai,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'absensi' => $absensi,
            'potongan_tetap' => $potongan_tetap,
            'potongan_absensi' => $potongan_absensi,
            'jumlah_terlambat' => $jumlah_terlambat,
            'total_awal' => $total_awal,
            'gajiPokok' => $pegawai->gaji_pokok,
            'insentifTetap' => $pegawai->insentif_kotor,
            'total_potongan_insentif_import' => $total_potongan_insentif_import, // <-- ini dikirim ke view
        ]);
    }

    public function hapus(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        Gaji::where([
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->delete();

        return redirect()->route('gaji.index', [
            'pegawai_id' => $request->pegawai_id,
            'tahun' => $request->tahun,
        ])->with('success', 'Data gaji berhasil dihapus.');
    }

public function simpan(Request $request)
{
    $request->validate([
        'pegawai_id' => 'required|exists:pegawais,id',
        'bulan' => 'required|integer|min:1|max:12',
        'tahun' => 'required|integer|min:2000|max:' . date('Y'),
        'gaji_pokok' => 'required',
        'insentif_tetap' => 'required',
        'potongan_tetap' => 'array',
        'potongan_tetap.*' => 'required',
        'potongan_insentif_import' => 'nullable',
        'insentif_import' => 'nullable',
        'potongan_lain' => 'nullable',
        'bonus' => 'nullable',
    ]);

    DB::beginTransaction();
    try {
        // Parse angka dari format "1.000.000" menjadi integer 1000000
        $gaji_pokok = (int)str_replace('.', '', $request->gaji_pokok);
        $insentif_tetap = (int)str_replace('.', '', $request->insentif_tetap);
        $potongan_lain = (int)str_replace('.', '', $request->potongan_lain ?? 0);
        $insentif_import = (int)str_replace('.', '', $request->insentif_import ?? 0);
        $bonus = (int)str_replace('.', '', $request->bonus ?? 0);

        // Potongan tetap bisa berupa array, bersihkan setiap elemennya
        $potongan_tetap = array_map(function($v) {
            return (int)str_replace('.', '', $v);
        }, $request->potongan_tetap ?? []);

        $potongan_insentif_import = (float)str_replace(',', '.', str_replace('.', '', $request->potongan_insentif_import ?? 0));
        $potongan_insentif_rupiah = $insentif_import * ($potongan_insentif_import / 100);

        $total_potongan = array_sum($potongan_tetap) + $potongan_lain + $potongan_insentif_rupiah;
        $gaji_bersih = max(0, ($gaji_pokok + $insentif_tetap + $bonus) - $total_potongan);

        Gaji::updateOrCreate([
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ], [
            'gaji_pokok' => $gaji_pokok,
            'insentif_tetap' => $insentif_tetap,
            'total_potongan' => $total_potongan,
            'gaji_bersih' => $gaji_bersih,
            'rincian_potongan' => json_encode([
                'potongan_tetap' => $potongan_tetap,
                'potongan_insentif_import' => [
                    'persen' => $potongan_insentif_import,
                    'rupiah' => $potongan_insentif_rupiah,
                ],
                'potongan_lain' => $potongan_lain,
                'bonus' => $bonus,
            ]),
        ]);

        DB::commit();
        return redirect()->route('gaji.index')->with('success', 'Gaji berhasil disimpan!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['msg' => 'Gagal menyimpan: ' . $e->getMessage()]);
    }
}

}
