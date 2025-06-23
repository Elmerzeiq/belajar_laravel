<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AbsensiImport;
use App\Models\AbsensiImport as AbsensiImportModel;
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

        AbsensiImportModel::where([
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->delete();

        Excel::import(new AbsensiImport($request->pegawai_id, $request->bulan, $request->tahun), $request->file('file_absensi'));

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
            'total_potongan_insentif_import' => $total_potongan_insentif_import,
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
            // Parse angka
            $gaji_pokok = (int)str_replace('.', '', $request->gaji_pokok);
            $insentif_tetap = (int)str_replace('.', '', $request->insentif_tetap);
            $potongan_lain = (int)str_replace('.', '', $request->potongan_lain ?? 0);
            $insentif_import = (int)str_replace('.', '', $request->insentif_import ?? 0);
            $bonus = (int)str_replace('.', '', $request->bonus ?? 0);

            // Potongan insentif import (%) -- TETAP pakai format persen
            $potongan_insentif_import = (float)str_replace(',', '.', str_replace('.', '', $request->potongan_insentif_import ?? 0));
            $potongan_insentif_rupiah = $insentif_tetap * ($potongan_insentif_import / 100); // FIX: harus dari insentif_tetap

            $potongan_tetap_ids = array_keys($request->potongan_tetap ?? []);
            $potongan_tetap_data = PotonganTetap::whereIn('id', $potongan_tetap_ids)->get()->keyBy('id');

            $potongan_gaji = 0;
            $potongan_insentif = 0;
            $potongan_total = 0;
            $rincian_potongan = [];
            $potongan_total_persen = [];

            foreach ($request->potongan_tetap as $id => $input_jumlah) {
                $pt = $potongan_tetap_data[$id] ?? null;
                if (!$pt) continue;

                // --- FIX: parsing jumlah ---
                if ($pt->tipe == 'persen') {
                    // Untuk persen, pakai format float saja
                    $jumlah = (float)str_replace(',', '.', str_replace('.', '', $input_jumlah));
                } else {
                    $jumlah = (float)str_replace('.', '', $input_jumlah);
                }

                if ($pt->tipe == 'persen') {
                    if ($pt->jenis_potongan == 'gaji_pokok') {
                        $nilai = $gaji_pokok * $jumlah / 100;
                        $potongan_gaji += $nilai;
                    } elseif ($pt->jenis_potongan == 'insentif') {
                        $nilai = $insentif_tetap * $jumlah / 100;
                        $potongan_insentif += $nilai;
                    } elseif ($pt->jenis_potongan == 'total') {
                        $potongan_total_persen[] = [
                            'id' => $id,
                            'persen' => $jumlah,
                            'pt' => $pt
                        ];
                    }
                } else {
                    if ($pt->jenis_potongan == 'gaji_pokok') {
                        $potongan_gaji += $jumlah;
                    } elseif ($pt->jenis_potongan == 'insentif') {
                        $potongan_insentif += $jumlah;
                    } elseif ($pt->jenis_potongan == 'total') {
                        $potongan_total += $jumlah;
                    }
                }

                $rincian_potongan['list'][] = [
                    'id' => $id,
                    'nama' => $pt->nama_potongan,
                    'tipe' => $pt->tipe,
                    'jenis_potongan' => $pt->jenis_potongan,
                    'jumlah' => $jumlah
                ];
            }

            $gaji_setelah_potongan_gaji = $gaji_pokok - $potongan_gaji;
            $insentif_setelah_potongan_insentif = $insentif_tetap - $potongan_insentif;

            // Potongan total persen setelah dikurangi potongan gaji/insentif
            $total_sementara = $gaji_setelah_potongan_gaji + $insentif_setelah_potongan_insentif;
            foreach ($potongan_total_persen as $ptp) {
                $nilai = $total_sementara * $ptp['persen'] / 100;
                $potongan_total += $nilai;
                $rincian_potongan['list'][] = [
                    'id' => $ptp['id'],
                    'nama' => $ptp['pt']->nama_potongan,
                    'tipe' => 'persen',
                    'jenis_potongan' => 'total',
                    'jumlah' => $ptp['persen'],
                    'nominal' => $nilai
                ];
            }

            $total_potongan = $potongan_gaji + $potongan_insentif + $potongan_total + $potongan_lain + $potongan_insentif_rupiah;

            // FINAL: hitung gaji bersih sama dengan di frontend
            $gaji_bersih = max(0, $gaji_setelah_potongan_gaji + $insentif_setelah_potongan_insentif + $bonus - $potongan_total - $potongan_lain - $potongan_insentif_rupiah);

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
                    'potongan_gaji' => $potongan_gaji,
                    'potongan_insentif' => $potongan_insentif,
                    'potongan_total' => $potongan_total,
                    'potongan_insentif_import' => [
                        'persen' => $potongan_insentif_import,
                        'rupiah' => $potongan_insentif_rupiah,
                    ],
                    'potongan_lain' => $potongan_lain,
                    'bonus' => $bonus,
                    'detail' => $rincian_potongan['list'] ?? [],
                ]),
            ]);

            $gajiRecord = Gaji::where([
                'pegawai_id' => $request->pegawai_id,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ])->first();

            DB::commit();

            return redirect()->route('gaji.index', [
                'pegawai_id' => $request->pegawai_id,
                'tahun' => $request->tahun,
            ])->with('success', 'Data gaji berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
