<?php

namespace App\Imports;

use App\Models\Absensi;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsensiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $pegawai = Pegawai::where('nip', $row['nip'])->first();

        if (!$pegawai) {
            return null;
        }

        // Konversi format Excel jika numeric
        $tanggal = is_numeric($row['tanggal'])
            ? Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d')
            : date('Y-m-d', strtotime($row['tanggal']));

        $jamMasuk = is_numeric($row['jam_masuk'])
            ? Date::excelToDateTimeObject($row['jam_masuk'])->format('H:i:s')
            : date('H:i:s', strtotime($row['jam_masuk']));

        $jamPulang = is_numeric($row['jam_pulang'])
            ? Date::excelToDateTimeObject($row['jam_pulang'])->format('H:i:s')
            : date('H:i:s', strtotime($row['jam_pulang']));

        $waktuMasukNormal = strtotime('08:00:00');
        $waktuPulangNormal = strtotime('16:30:00');

        $terlambatMenit = max(0, (strtotime($jamMasuk) - $waktuMasukNormal) / 60);
        $pulangCepatMenit = max(0, ($waktuPulangNormal - strtotime($jamPulang)) / 60);

        $hitPotongan = function ($menit) {
            if ($menit == 0) return 0;
            elseif ($menit <= 30) return 0.5;
            elseif ($menit <= 60) return 1;
            elseif ($menit <= 90) return 1.5;
            else return 2;
        };

        $potonganTerlambat = $hitPotongan($terlambatMenit);
        $potonganPulangCepat = $hitPotongan($pulangCepatMenit);
        $totalPotongan = $potonganTerlambat + $potonganPulangCepat;

        return new Absensi([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $tanggal,
            'jam_masuk' => $jamMasuk,
            'jam_pulang' => $jamPulang,
            'potongan_persen' => $totalPotongan,
        ]);
    }
}
