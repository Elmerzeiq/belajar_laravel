<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'potongan_persen'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
