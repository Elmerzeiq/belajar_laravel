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
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y'];
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
        // Validasi input request
        $request->validate([
            'file_absen' => 'required|mimes:xlsx,xls', // Wajib upload file Excel
            'pegawai_id' => 'required|integer', // Wajib ada ID pegawai
            'bulan' => 'required|integer', // Wajib ada bulan
            'tahun' => 'required|integer', // Wajib ada tahun
        ]);

        $insentif = $request->insentif; // Ambil insentif dari input
        $data = Excel::toArray([], $request->file('file_absen')->getRealPath()); // Baca isi file Excel
        $sheet = $data[0]; // Ambil sheet pertama

        // Mencari header file
        $header = null;
        $headerIndex = null;
        foreach ($sheet as $index => $row) { // Loop setiap baris
            if (in_array('Tanggal', $row)) { // Jika ketemu kolom Tanggal
                $header = $row; // Simpan header
                $headerIndex = $index; // Simpan posisi header
                break; // Hentikan pencarian
            }
        }

        // Jika header tidak ditemukan
        if (!$header) {
            return back()->with('error', 'Header kolom tidak ditemukan. Pastikan format file Excel sesuai.');
        }

        // Kolom yang harus ada
        $wantedCols = ['Tanggal', 'Jam Masuk', 'Jam Pulang', 'Scan Masuk', 'Scan Keluar'];
        $colIndexes = [];

        // Cari posisi masing-masing kolom yang dibutuhkan
        foreach ($wantedCols as $wanted) {
            foreach ($header as $i => $colName) {
                if (strcasecmp(trim($colName), $wanted) == 0) {
                    $colIndexes[$wanted] = $i;
                    break;
                }
            }
        }

        // Jika ada kolom yang tidak ditemukan
        if (count($colIndexes) < count($wantedCols)) {
            return back()->with('error', 'Beberapa kolom tidak ditemukan. Pastikan format file Excel sesuai.');
        }

        $dataRows = array_slice($sheet, $headerIndex + 1); // Ambil data mulai setelah header
        $rows = []; // Simpan hasil proses per baris
        $total_potongan_persen = 0.0; // Simpan total potongan pegawai

        foreach ($dataRows as $row) { // Loop setiap data
            $tanggal = $row[$colIndexes['Tanggal']] ?? null;
            $jam_masuk = $this->excelTimeToHM($row[$colIndexes['Jam Masuk']] ?? null);
            $jam_pulang = $this->excelTimeToHM($row[$colIndexes['Jam Pulang']] ?? null);
            $scan_masuk = $this->excelTimeToHM($row[$colIndexes['Scan Masuk']] ?? null);
            $scan_keluar = $this->excelTimeToHM($row[$colIndexes['Scan Keluar']] ?? null);

            if (empty($tanggal) || empty($jam_masuk) || empty($jam_pulang)) continue;

            $tanggalObj = $this->parseTanggal($tanggal); // Konversi tanggal
            if (!$tanggalObj) continue;

            $hari = $tanggalObj->locale('id')->isoFormat('dddd'); // Ambil hari dalam bahasa Indonesia

            // Tentukan waktu masuk dan pulang normal
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

            $terlambat_menit = 0; // Jumlah menit terlambat
            $pulang_cepat_menit = 0; // Jumlah menit pulang cepat
            $potongan_terlambat = 0; // Potongan karena terlambat
            $potongan_pulang_cepat = 0; // Potongan karena pulang cepat
            $potongan_persen = 0; // Total potongan hari itu
            $tidak_hadir = false; // Apakah pegawai tidak hadir

            // Jika tidak ada scan masuk dan keluar
            if (empty($scan_masuk) && empty($scan_keluar)) {
                $potongan_persen = 3.0; // Potongan 3% jika tidak hadir
                $tidak_hadir = true;
            } else {
                // Hitung keterlambatan
                if ($waktu_masuk_aktual && $waktu_masuk_aktual->gt($waktu_masuk_normal)) {
                    $terlambat_menit = $waktu_masuk_normal->diffInMinutes($waktu_masuk_aktual);
                    $potongan_terlambat = $this->hitungPotonganTerpisah($terlambat_menit);
                }

                // Hitung pulang cepat
                if ($waktu_pulang_aktual && $waktu_pulang_aktual->lt($waktu_pulang_normal)) {
                    $pulang_cepat_menit = abs($waktu_pulang_normal->diffInMinutes($waktu_pulang_aktual, false));
                    $potongan_pulang_cepat = $this->hitungPotonganTerpisah($pulang_cepat_menit);
                }

                $potongan_persen = $potongan_terlambat + $potongan_pulang_cepat;
            }

            $total_potongan_persen += $potongan_persen;

            // Simpan hasil per hari
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

        // Buat array hasil perhitungan
        $hasil_perhitungan = [
            'rows' => $rows,
            'insentif' => $insentif,
            'total_potongan_persen' => $total_potongan_persen,
        ];

        // Simpan atau update ke tabel payroll_results
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

        // Redirect ke halaman preview gaji
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
