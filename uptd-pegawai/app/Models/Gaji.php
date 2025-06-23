<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    protected $fillable = [
        'pegawai_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'insentif_tetap',    // Tambahkan ini
        'total_potongan',
        'gaji_bersih',
        'bonus',             // Tambahkan ini
        'rincian_potongan'
    ];

    protected $casts = [
        'rincian_potongan' => 'array',
    ];

    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
