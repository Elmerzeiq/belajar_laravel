<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function slip($id)
    {
        $gaji = Gaji::with('pegawai')->findOrFail($id);
        $pegawai = $gaji->pegawai;
        $bulan = $gaji->bulan;
        $tahun = $gaji->tahun;

        $pdf = Pdf::loadView('gaji.payroll_pdf', [
            'gaji' => $gaji,
            'pegawai' => $pegawai,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);

        return $pdf->stream('Slip_Gaji_'.$pegawai->nama.'_'.$bulan.'_'.$tahun.'.pdf');
    }
}
