<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\PayrollResult;
use App\Models\Pegawai;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsenImportController extends Controller
{
    // Konversi dari nilai Excel Time ke format H:i
    private function excelTimeToHM($excelTime)
    {
        // Jika nilai kosong, kembalikan null
        if (is_null($excelTime) || $excelTime === '') return null;

        // Jika format sudah string jam (contoh: 07:30)
        if (preg_match('/^\d{1,2}:\d{2}$/', $excelTime)) {
            return $excelTime; // Langsung kembalikan jika sudah dalam format jam
        }

        // Jika format serial Excel time
        try {
            $dateTime = Date::excelToDateTimeObject($excelTime); // Konversi serial Excel ke DateTime
            return $dateTime->format('H:i'); // Format ke jam:menit
        } catch (\Exception $e) {
            \Log::error("Gagal konversi jam: $excelTime"); // Log error jika gagal konversi
            return null; // Kembalikan null jika gagal
        }
    }

    // Hitung potongan maksimal 1.5%
    private function hitungPotonganTerpisah($menit)
    {
        // Perhitungan potongan berdasarkan menit keterlambatan atau pulang cepat
        return match (true) {
            $menit == 0 => 0, // Tidak terlambat, tidak ada potongan
            $menit <= 30 => 0.5, // Terlambat/pulang cepat 1-30 menit potong 0.5%
            $menit <= 60 => 1, // Terlambat/pulang cepat 31-60 menit potong 1%
            default => 1.5, // Lebih dari 60 menit potong 1.5%
        };
    }

    // Konversi tanggal dari berbagai format string atau serial Excel
    private function parseTanggal($tanggal)
    {
        // Jika data numeric berarti format serial Excel
        if (is_numeric($tanggal)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($tanggal)); // Konversi ke Carbon
            } catch (\Exception $e) {
                \Log::error("Gagal konversi serial Excel date: $tanggal"); // Log jika gagal konversi
                return null; // Kembalikan null jika gagal
            }
        }

        // Coba beberapa format string tanggal
        $formats = ['d/m/Y','Y-m-d','Y-d-m'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $tanggal); // Coba konversi dengan format yang diuji
            } catch (\Exception $e) {
                continue; // Lanjut jika format tidak cocok
            }
        }

        try {
            return Carbon::parse($tanggal); // Coba parsing otomatis
        } catch (\Exception $e) {
            \Log::error("Gagal parsing tanggal: $tanggal"); // Log jika gagal parsing
            return null; // Kembalikan null jika gagal
        }
    }

    // Fungsi utama untuk proses import absen
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

        // Cari header
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

        // Tambahkan 'Pengecualian' sebagai kolom wajib
        $wantedCols = ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Scan Masuk', 'Scan Keluar', 'Pengecualian'];
        $colIndexes = [];
        foreach ($wantedCols as $wanted) {
            foreach ($header as $i => $colName) {
                if (strcasecmp(trim($colName), $wanted) == 0) {
                    $colIndexes[$wanted] = $i;
                    break;
                }
            }
        }
        // Cek minimal kolom utama, abaikan pengecualian jika tidak ada
        if (count($colIndexes) < count($wantedCols) - 1) {
            return back()->with('error', 'Beberapa kolom utama tidak ditemukan. Pastikan format file Excel sesuai.');
        }

        $dataRows = array_slice($sheet, $headerIndex + 1);
        $rows = [];
        $total_potongan_persen = 0.0;

        foreach ($dataRows as $row) {
            $tanggal = $row[$colIndexes['Tanggal']] ?? null;
            $jam_masuk = $this->excelTimeToHM($row[$colIndexes['Jam Masuk']] ?? null);
            $jam_pulang = $this->excelTimeToHM($row[$colIndexes['Jam Pulang']] ?? null);
            $scan_masuk = $this->excelTimeToHM($row[$colIndexes['Scan Masuk']] ?? null);
            $scan_keluar = $this->excelTimeToHM($row[$colIndexes['Scan Keluar']] ?? null);
            // Ambil pengecualian DENGAN kapitalisasi asli
            $pengecualian = isset($colIndexes['Pengecualian']) ? trim($row[$colIndexes['Pengecualian']] ?? '') : '';

            if (empty($tanggal) || empty($jam_masuk) || empty($jam_pulang)) continue;
            $tanggalObj = $this->parseTanggal($tanggal);
            if (!$tanggalObj) continue;
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

            // LOGIKA PENGECUALIAN (case-insensitive)
            $pengecualian_lower = strtolower($pengecualian);
            $is_pengecualian = in_array($pengecualian_lower, ['other', 'dinas luar']);

            if ($is_pengecualian) {
                // Tidak kena denda walau scan kosong
                $potongan_terlambat = 0;
                $potongan_pulang_cepat = 0;
                $potongan_persen = 0;
                $tidak_hadir = false;
            } else {
                // Logika lama
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
            }

            $total_potongan_persen += $potongan_persen;

            $rows[] = [
                'tanggal' => $tanggalObj->format('d-m-Y'),
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
                'pengecualian' => $pengecualian, // simpan dengan kapitalisasi asli
            ];
        }

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

    // Menampilkan halaman preview hasil import
    public function preview($pegawai_id, $bulan, $tahun)
    {
        $payroll = PayrollResult::where('pegawai_id', $pegawai_id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first(); // Cari data payroll pegawai

        if (!$payroll) {
            return back()->with('error', 'Data belum diimport untuk periode ini.'); // Jika tidak ada, munculkan error
        }

        $pegawai = Pegawai::findOrFail($pegawai_id); // Ambil data pegawai
        $hasil = $payroll->hasil_perhitungan; // Ambil hasil perhitungan gaji

        return view('gaji.gaji_preview', [ // Tampilkan halaman preview
            'rows' => $hasil['rows'],
            'pegawai_id' => $pegawai_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'insentif' => $hasil['insentif'] ?? null,
            'total_potongan_persen' => $hasil['total_potongan_persen'] ?? 0,
            'pegawai' => $pegawai,
        ]);
    }
}
