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
    //
}
