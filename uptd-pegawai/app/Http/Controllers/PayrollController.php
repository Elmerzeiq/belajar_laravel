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

    // TAMBAHKAN METHOD INI:
    public function payrollPdf($pegawai_id, $bulan, $tahun)
    {
        $gaji = Gaji::with('pegawai')
            ->where('pegawai_id', $pegawai_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->firstOrFail();

        $pegawai = $gaji->pegawai;

        $pdf = Pdf::loadView('gaji.payroll_pdf', [
            'gaji' => $gaji,
            'pegawai' => $pegawai,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);

        return $pdf->stream('Slip_Gaji_'.$pegawai->nama.'_'.$bulan.'_'.$tahun.'.pdf');
    }
}
