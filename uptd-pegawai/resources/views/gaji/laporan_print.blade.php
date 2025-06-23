<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            display: block;
            margin: 0 auto 0 auto;
            width: 100%;
            max-width: 600px;
        }

        .clear {
            clear: both;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th, .table td {
            border: 1px solid #222;
            padding: 6px 8px;
            font-size: 12px;
        }

        .table th {
            background: #f4f4f4;
        }

        .section-title {
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 6px;
        }

        .section-space {
            margin-bottom: 18px;
        }

        .signature {
            margin-top: 32px;
            width: 100%;
        }

        .signature td {
            border: none;
        }
    </style>
</head>

<body>
    {{-- Header image only --}}
    <div class="header">
        <img src="{{ public_path('img/payrollheader.png') }}" class="logo">
        <div style="font-size: 1.2em; font-weight: bold; margin-top: 6px;">
            Laporan Gaji Pegawai
        </div>
        <div style="margin-top: 2px;">
            Periode: <strong>{{ $bulanList[$filterBulan] ?? '' }} {{ $filterTahun }}</strong>
        </div>
    </div>

    <div style="margin-bottom: 10px;">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th style="text-align:right;">Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $gaji)
                <tr>
                    <td style="text-align:center;">{{ $index+1 }}</td>
                    <td>{{ $gaji->pegawai->nama ?? '-' }}</td>
                    <td>{{ $gaji->pegawai->nip ?? '-' }}</td>
                    <td>{{ $gaji->pegawai->jabatan ?? '-' }}</td>
                    <td style="text-align:right;">
                        {{ number_format($gaji->gaji_bersih ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada data gaji pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-space"></div>

    <table class="signature">
        <tr>
            <td style="width:65%; vertical-align:top;">
                <div>Keterangan:</div>
                <div style="margin-bottom:8px;">Laporan ini dicetak untuk keperluan administrasi internal.</div>
            </td>
            <td style="width:35%; text-align:center;">
                Palembang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                Bendahara Pengeluaran,<br><br><br><br>
                <strong>Nurlaila, SE, MM</strong><br>
                Pembina (IV/a)<br>
                NIP. 19761228 200003 2 002
            </td>
        </tr>
    </table>
</body>

</html>
