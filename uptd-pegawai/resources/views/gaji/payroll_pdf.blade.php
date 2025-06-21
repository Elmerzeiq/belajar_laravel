<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Slip Gaji Pegawai</h2>
        <div>Periode: <strong>{{ bulanIndo($bulan) }} {{ $tahun }}</strong></div>
    </div>
    <table>
        <tr>
            <td>Nama Pegawai</td>
            <td>: {{ $pegawai->nama }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>: {{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td>Total Gaji Bersih</td>
            <td>: Rp {{ number_format($gaji->gaji_bersih,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td>: Rp {{ number_format($gaji->gaji_pokok,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Insentif Tetap</td>
            <td>: Rp {{ number_format($gaji->insentif_tetap,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Potongan</td>
            <td>: Rp {{ number_format($gaji->total_potongan,0,',','.') }}</td>
        </tr>
    </table>
    <br>
    <small>Dicetak pada {{ date('d-m-Y H:i') }}</small>
</body>
</html>
