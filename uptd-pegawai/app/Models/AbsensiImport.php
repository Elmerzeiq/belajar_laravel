<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiImport extends Model
{
    protected $fillable = [
        'pegawai_id','bulan','tahun','tanggal','jam_masuk','jam_pulang',
        'scan_masuk','scan_keluar','terlambat','plg_cpt','lembur','jml_hadir','pengecualian'
    ];
}
