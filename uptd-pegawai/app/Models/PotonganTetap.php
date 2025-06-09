<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotonganTetap extends Model
{
    use HasFactory;

    protected $table = 'potongan_tetap'; // <= Tambahkan baris ini

    protected $fillable = ['nama_potongan', 'tipe', 'jumlah'];
}
