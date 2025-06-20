<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $guarded = ['id'];

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    // Tambahkan relasi ke Gaji:
    public function gaji()
    {
        return $this->hasMany(Gaji::class, 'pegawai_id');
    }
}
