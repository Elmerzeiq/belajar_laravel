<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AbsenImportController extends Controller
{
    // Fungsi konversi excel time ke string jam:menit
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

    // Fungsi hitung potongan persen per pelanggaran
    private function hitungPotongan($menit)
    {
        return match (true) {
            $menit == 0 => 0,
            $menit <= 30 => 0.5,
            $menit <= 60 => 1,
            $menit <= 90 => 1.5,
            $menit <= 120 => 2,
            $menit <= 150 => 2.5,
            default => 3,
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

        $wantedCols = [
            'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Scan Masuk', 'Scan Keluar'
        ];

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
            $scan_masuk = $row[$colIndexes['Scan Masuk']] ?? null;
            $scan_keluar = $row[$colIndexes['Scan Keluar']] ?? null;

            $scan_masuk = $this->excelTimeToHM($scan_masuk);
            $scan_keluar = $this->excelTimeToHM($scan_keluar);

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
            $waktu_pulang_normal = (strtolower($hari) == 'jumat' || strtolower($hari) == 'friday') ?
                $tanggalObj->copy()->setTime(16, 30, 0) :
                $tanggalObj->copy()->setTime(16, 0, 0);

            try {
                $waktu_masuk_aktual = $scan_masuk ? Carbon::createFromFormat('H:i', $scan_masuk)->setDateFrom($tanggalObj) : null;
            } catch (\Exception $e) {
                \Log::error("Gagal parsing scan masuk: $scan_masuk");
                $waktu_masuk_aktual = null;
            }

            try {
                $waktu_pulang_aktual = $scan_keluar ? Carbon::createFromFormat('H:i', $scan_keluar)->setDateFrom($tanggalObj) : null;
            } catch (\Exception $e) {
                \Log::error("Gagal parsing scan keluar: $scan_keluar");
                $waktu_pulang_aktual = null;
            }

            // Terlambat: scan masuk > jam masuk normal

            $terlambat_menit = 0;
            if ($waktu_masuk_aktual && $waktu_masuk_normal && $waktu_masuk_aktual->gt($waktu_masuk_normal)) {
                $terlambat_menit = $waktu_masuk_normal->diffInMinutes($waktu_masuk_aktual);
            }
            // Pulang cepat: scan keluar < jam pulang normal
            $pulang_cepat_menit = 0;
            if ($waktu_pulang_aktual && $waktu_pulang_normal && $waktu_pulang_aktual->lt($waktu_pulang_normal)) {
                // Perbaikan: ABS agar tidak pernah minus
                $pulang_cepat_menit = abs($waktu_pulang_normal->diffInMinutes($waktu_pulang_aktual, false));
            }

            // Gabungkan menit keterlambatan dan pulang cepat
            $total_menit = $terlambat_menit + $pulang_cepat_menit;

            // Hitung potongan hanya sekali untuk total menit (maksimal 2%)
            $potongan_persen = $this->hitungPotongan($total_menit);
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
                'potongan_persen' => $potongan_persen,
            ];
        }

        session([
            'rows' => $rows,
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'insentif' => $insentif,
            'total_potongan_persen' => $total_potongan_persen,
        ]);

        return redirect()->route('gaji.preview', [
            'pegawai_id' => $request->pegawai_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);
    }

    public function preview()
    {
        $rows = session('rows');
        $pegawai_id = session('pegawai_id');
        $bulan = session('bulan');
        $tahun = session('tahun');
        $insentif = session('insentif');
        $total_potongan_persen = session('total_potongan_persen');

        return view('gaji.gaji_preview', compact('rows', 'pegawai_id', 'bulan', 'tahun', 'insentif', 'total_potongan_persen'));
    }
}