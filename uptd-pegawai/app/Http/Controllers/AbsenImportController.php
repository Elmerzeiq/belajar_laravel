<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\PayrollResult;

class AbsenImportController extends Controller
{
    // Konversi dari nilai Excel Time ke format H:i
    private function excelTimeToHM($excelTime)
    {
        if (is_null($excelTime) || $excelTime === '') return null;
        if (preg_match('/^\d{1,2}:\d{2}$/', $excelTime)) {
            return $excelTime;
        }
        $totalSeconds = round($excelTime * 24 * 60 * 60);
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // Hitung potongan maksimal 1.5%
    private function hitungPotonganTerpisah($menit)
    {
        return match (true) {
            $menit == 0 => 0,
            $menit <= 30 => 0.5,
            $menit <= 60 => 1,
            default => 1.5,
        };
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_absen' => 'required|mimes:xlsx,xls',
            'pegawai_id' => 'required|integer',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        $insentif = $request->insentif;
        $data = Excel::toArray([], $request->file('file_absen')->getRealPath());
        $sheet = $data[0];

        // Temukan header
        $header = null;
        $headerIndex = null;
        foreach ($sheet as $index => $row) {
            if (in_array('Tanggal', $row)) {
                $header = $row;
                $headerIndex = $index;
                break;
            }
        }

        if (!$header) {
            return back()->with('error', 'Header kolom tidak ditemukan. Pastikan format file Excel sesuai.');
        }

        $wantedCols = ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Scan Masuk', 'Scan Keluar'];
        $colIndexes = [];

        foreach ($wantedCols as $wanted) {
            foreach ($header as $i => $colName) {
                if (strcasecmp(trim($colName), $wanted) == 0) {
                    $colIndexes[$wanted] = $i;
                    break;
                }
            }
        }

        if (count($colIndexes) < count($wantedCols)) {
            return back()->with('error', 'Beberapa kolom tidak ditemukan. Pastikan format file Excel sesuai.');
        }

        $dataRows = array_slice($sheet, $headerIndex + 1);
        $rows = [];
        $total_potongan_persen = 0.0;

        foreach ($dataRows as $row) {
            $tanggal = $row[$colIndexes['Tanggal']] ?? null;
            $jam_masuk = $row[$colIndexes['Jam Masuk']] ?? null;
            $jam_pulang = $row[$colIndexes['Jam Pulang']] ?? null;
            $scan_masuk = $this->excelTimeToHM($row[$colIndexes['Scan Masuk']] ?? null);
            $scan_keluar = $this->excelTimeToHM($row[$colIndexes['Scan Keluar']] ?? null);

            if (empty($tanggal) || empty($jam_masuk) || empty($jam_pulang)) continue;

            try {
                $tanggalObj = Carbon::createFromFormat('Y-m-d', $tanggal);
            } catch (\Exception $e) {
                try {
                    $tanggalObj = Carbon::createFromFormat('d/m/Y', $tanggal);
                } catch (\Exception $e2) {
                    \Log::error("Gagal parsing tanggal: $tanggal");
                    continue;
                }
            }

            $hari = $tanggalObj->locale('id')->isoFormat('dddd');
            $waktu_masuk_normal = $tanggalObj->copy()->setTime(7, 30, 0);
            $waktu_pulang_normal = in_array(strtolower($hari), ['jumat', 'friday'])
                ? $tanggalObj->copy()->setTime(16, 30, 0)
                : $tanggalObj->copy()->setTime(16, 0, 0);

            try {
                $waktu_masuk_aktual = $scan_masuk ? Carbon::createFromFormat('H:i', $scan_masuk)->setDateFrom($tanggalObj) : null;
            } catch (\Exception $e) {
                $waktu_masuk_aktual = null;
            }

            try {
                $waktu_pulang_aktual = $scan_keluar ? Carbon::createFromFormat('H:i', $scan_keluar)->setDateFrom($tanggalObj) : null;
            } catch (\Exception $e) {
                $waktu_pulang_aktual = null;
            }

            $terlambat_menit = 0;
            $pulang_cepat_menit = 0;
            $potongan_terlambat = 0;
            $potongan_pulang_cepat = 0;
            $potongan_persen = 0;
            $tidak_hadir = false;

            // Jika tidak ada scan masuk & keluar
            if (empty($scan_masuk) && empty($scan_keluar)) {
                $potongan_persen = 3.0;
                $tidak_hadir = true;
            } else {
                if ($waktu_masuk_aktual && $waktu_masuk_aktual->gt($waktu_masuk_normal)) {
                    $terlambat_menit = $waktu_masuk_normal->diffInMinutes($waktu_masuk_aktual);
                    $potongan_terlambat = $this->hitungPotonganTerpisah($terlambat_menit);
                }

                if ($waktu_pulang_aktual && $waktu_pulang_aktual->lt($waktu_pulang_normal)) {
                    $pulang_cepat_menit = abs($waktu_pulang_normal->diffInMinutes($waktu_pulang_aktual, false));
                    $potongan_pulang_cepat = $this->hitungPotonganTerpisah($pulang_cepat_menit);
                }

                $potongan_persen = $potongan_terlambat + $potongan_pulang_cepat;
            }

            $total_potongan_persen += $potongan_persen;

            $rows[] = [
                'tanggal' => $tanggalObj->format('Y-m-d'),
                'hari' => ucfirst($hari),
                'jam_masuk' => $jam_masuk,
                'jam_pulang' => $jam_pulang,
                'scan_masuk' => $scan_masuk,
                'scan_keluar' => $scan_keluar,
                'terlambat_menit' => $terlambat_menit,
                'pulang_cepat_menit' => $pulang_cepat_menit,
                'potongan_terlambat' => $potongan_terlambat,
                'potongan_pulang_cepat' => $potongan_pulang_cepat,
                'potongan_persen' => $potongan_persen,
                'tidak_hadir' => $tidak_hadir,
            ];
        }

        // Simpan hasil perhitungan ke tabel payroll_results
        $hasil_perhitungan = [
            'rows' => $rows,
            'insentif' => $insentif,
            'total_potongan_persen' => $total_potongan_persen,
        ];

        PayrollResult::updateOrCreate(
            [
                'pegawai_id' => $request->pegawai_id,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
            ],
            [
                'hasil_perhitungan' => $hasil_perhitungan,
            ]
        );

        return redirect()->route('gaji.preview', [
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);
    }

    // TERIMA PARAMETER LANGSUNG DARI ROUTE
    public function preview($pegawai_id, $bulan, $tahun)
    {
        $payroll = PayrollResult::where('pegawai_id', $pegawai_id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        if (!$payroll) {
            return back()->with('error', 'Data belum diimport untuk periode ini.');
        }

        $hasil = $payroll->hasil_perhitungan;

        return view('gaji.gaji_preview', [
            'rows' => $hasil['rows'],
            'pegawai_id' => $pegawai_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'insentif' => $hasil['insentif'] ?? null,
            'total_potongan_persen' => $hasil['total_potongan_persen'] ?? 0,
        ]);
    }
}
