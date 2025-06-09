@extends('layouts.mantis')

@section('content')
<div class="">
    <h3>Rekap Gaji Bulan {{ \Carbon\Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM Y') }}</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Gaji Pokok</th>
                <th>Potongan Pajak</th>
                <th>Potongan BPJS</th>
                <th>Insentif Kotor</th>
                <th>Potongan Keterlambatan (%)</th>
                <th>Insentif Bersih</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $pegawai)
                <tr>
                    <td>{{ $pegawai->nama }}</td>
                    <td>{{ number_format($pegawai->gaji_pokok, 0, ',', '.') }}</td>
                    <td>{{ number_format($pegawai->potongan_pajak, 0, ',', '.') }}</td>
                    <td>{{ number_format($pegawai->potongan_bpjs, 0, ',', '.') }}</td>
                    <td>{{ number_format($pegawai->insentif_kotor, 0, ',', '.') }}</td>
                    <td>{{ number_format($pegawai->total_potongan, 2) }}%</td>
                    <td>{{ number_format($pegawai->insentif_bersih, 0, ',', '.') }}</td>
                    <td><strong>{{ number_format($pegawai->gaji_total, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
