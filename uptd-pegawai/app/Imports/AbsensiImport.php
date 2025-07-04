<?php

namespace App\Imports;

use App\Models\AbsensiImport as AbsensiImportModel; // Alias model
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class AbsensiImport implements ToCollection, WithHeadingRow
{
    protected $pegawai_id, $bulan, $tahun;

    public function __construct($pegawai_id, $bulan, $tahun)
    {
        $this->pegawai_id = $pegawai_id; // Simpan ID pegawai
        $this->bulan = $bulan; // Simpan bulan
        $this->tahun = $tahun; // Simpan tahun
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['tanggal'])) continue; // Lewati jika kolom tanggal kosong

            $tanggal = $row['tanggal'];

            // Cek jika tanggal berbentuk numeric (format serial Excel)
            if (is_numeric($tanggal)) {
                try {
                    $tanggal = Date::excelToDateTimeObject($tanggal)->format('Y-m-d'); // Konversi ke Y-m-d
                } catch (\Exception $e) {
                    continue; // Lewati baris jika gagal parsing
                }
            } else {
                try {
                    $tanggal = Carbon::parse($tanggal)->format('Y-d-m'); // Konversi string tanggal ke Y-d-m
                } catch (\Exception $e) {
                    continue; // Lewati baris jika gagal parsing
                }
            }

            // Simpan ke database (tanpa lembur)
            AbsensiImportModel::create([
                'pegawai_id'   => $this->pegawai_id,
                'bulan'        => $this->bulan,
                'tahun'        => $this->tahun,
                'tanggal'      => $tanggal,
                'jam_masuk'    => $row['jam_masuk'] ?? null,
                'jam_pulang'   => $row['jam_pulang'] ?? null,
                'scan_masuk'   => $row['scan_masuk'] ?? null,
                'scan_keluar'  => $row['scan_keluar'] ?? null,
                'terlambat'    => $row['terlambat'] ?? null,
                'plg_cpt'      => $row['plg_cpt'] ?? null,
                'jml_hadir'    => $row['jml_hadir'] ?? null,
                'pengecualian' => $row['pengecualian'] ?? null,
            ]);
        }
    }
}
