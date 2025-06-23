<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollResult extends Model
{
    use HasFactory;

    protected $fillable = ['pegawai_id', 'tahun', 'bulan', 'hasil_perhitungan'];

    protected $casts = [
        'hasil_perhitungan' => 'array',
    ];
}
