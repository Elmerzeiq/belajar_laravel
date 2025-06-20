<?php

namespace App\Imports;

use App\Models\AbsensiImport as AbsensiImportModel; // Beri alias di sini!
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AbsensiImport implements ToCollection, WithHeadingRow
{
    protected $pegawai_id, $bulan, $tahun;

    public function __construct($pegawai_id, $bulan, $tahun)
    {
        $this->pegawai_id = $pegawai_id;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['tanggal'])) continue;

            AbsensiImportModel::create([
                'pegawai_id'   => $this->pegawai_id,
                'bulan'        => $this->bulan,
                'tahun'        => $this->tahun,
                'tanggal'      => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d'),
                'jam_masuk'    => $row['jam_masuk'] ?? null,
                'jam_pulang'   => $row['jam_pulang'] ?? null,
                'scan_masuk'   => $row['scan_masuk'] ?? null,
                'scan_keluar'  => $row['scan_keluar'] ?? null,
                'terlambat'    => $row['terlambat'] ?? null,
                'plg_cpt'      => $row['plg_cpt'] ?? null,
                'lembur'       => $row['lembur'] ?? null,
                'jml_hadir'    => $row['jml_hadir'] ?? null,
                'pengecualian' => $row['pengecualian'] ?? null,
            ]);
        }
    }
}
