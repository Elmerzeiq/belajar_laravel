<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; }
        .logo { display: block; margin: 0 auto 0 auto; width: 100%; max-width: 600px; }
        .clear { clear: both; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .section-title { font-weight: bold; margin-top: 18px; margin-bottom:6px;}
        .section-space { margin-bottom: 18px; }
        .signature { margin-top: 32px; width: 100%; }
        .signature td { border: none; }
        td, th { font-size: 12px; }
    </style>
</head>
<body>
    {{-- Header image only --}}
    <div class="header">
        <img src="{{ public_path('img/payrollheader.png') }}" class="logo">
    </div>

    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td style="width: 28%;">Nomor Urut</td>
            <td style="width: 2%;">:</td>
            <td>{{ $pegawai->nip }}</td>
            <td style="width: 22%;">Bulan</td>
            <td style="width: 2%;">:</td>
            <td>{{ bulanIndo($bulan) }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td>Nama Pegawai</td>
            <td>:</td>
            <td>{{ $pegawai->nama }}</td>
            <td>Pangkat/Golongan</td>
            <td>:</td>
            <td>{{ $pegawai->pangkat ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $pegawai->nik ?? '-' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <div class="section-space"></div>

    <div style="margin-bottom: 10px;">
        Mempunyai Penghasilan Yang Diperinci Sebagai Berikut :
    </div>
    <table style="width: 100%; margin-bottom: 16px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%;">Gaji Pokok</td>
            <td style="width: 2%;">:</td>
            <td style="width: 25%; text-align: right;">Rp {{ number_format($gaji->gaji_pokok,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan Tetap Pegawai (TPP/Insentif/Remunerasi)</td>
            <td>:</td>
            <td style="text-align: right;">Rp {{ number_format($gaji->insentif_tetap,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Bonus</td>
            <td>:</td>
            <td style="text-align: right;">Rp {{ number_format($gaji->bonus ?? 0,0,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight:bold;">Jumlah Penghasilan Kotor</td>
            <td style="font-weight:bold; text-align: right;">
                Rp {{ number_format(($gaji->gaji_pokok + $gaji->insentif_tetap + ($gaji->bonus ?? 0)),0,',','.') }}
            </td>
        </tr>
    </table>

    <div class="section-space"></div>
    <div class="section-title">Potongan-potongan :</div>
    <table style="width: 100%; margin-bottom: 16px;" cellpadding="0" cellspacing="0">
        @php
            $rincian = json_decode($gaji->rincian_potongan ?? '{}', true);
        @endphp
        @if(isset($rincian['detail']))
            @foreach($rincian['detail'] as $pot)
                <tr>
                    <td style="width: 50%;">
                        {{ $pot['nama'] ?? '-' }}
                        @if(isset($pot['tipe']) && $pot['tipe'] === 'persen')
                            ({{ floatval($pot['jumlah']) }}%)
                        @endif
                    </td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 25%; text-align: right;">
                        @if(isset($pot['tipe']) && $pot['tipe'] === 'persen')
                            @if(isset($pot['nominal']))
                                Rp {{ number_format($pot['nominal'],0,',','.') }}
                            @else
                                {{-- Jika data nominal tidak ada, hitung manual sesuai jenis potongan --}}
                                @php
                                    $persen = floatval($pot['jumlah']);
                                    $nominal = 0;
                                    if(isset($pot['jenis_potongan'])) {
                                        if($pot['jenis_potongan'] == 'gaji_pokok') {
                                            $nominal = $gaji->gaji_pokok * $persen / 100;
                                        } elseif($pot['jenis_potongan'] == 'insentif') {
                                            $nominal = $gaji->insentif_tetap * $persen / 100;
                                        } elseif($pot['jenis_potongan'] == 'total') {
                                            $nominal = ($gaji->gaji_pokok + $gaji->insentif_tetap) * $persen / 100;
                                        }
                                    }
                                @endphp
                                Rp {{ number_format($nominal,0,',','.') }}
                            @endif
                        @else
                            Rp {{ number_format($pot['jumlah'] ?? 0,0,',','.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        {{-- Potongan Denda Absensi --}}
        @if(isset($rincian['potongan_insentif_import']) && (isset($rincian['potongan_insentif_import']['rupiah']) && floatval($rincian['potongan_insentif_import']['rupiah']) > 0))
            <tr>
                <td>
                    Potongan Denda Absensi
                    @if(isset($rincian['potongan_insentif_import']['persen']))
                        ({{ $rincian['potongan_insentif_import']['persen'] }}%)
                    @endif
                </td>
                <td>:</td>
                <td style="text-align: right;">
                    Rp {{ number_format($rincian['potongan_insentif_import']['rupiah'] ?? 0,0,',','.') }}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="2" style="font-weight:bold; padding-top:8px;">Jumlah Potongan</td>
            <td style="font-weight:bold; text-align: right; padding-top:8px;">
                Rp {{ number_format($gaji->total_potongan,0,',','.') }}
            </td>
        </tr>
    </table>

    <div class="section-space"></div>
    <div class="section-title">Jumlah yang dibayarkan :</div>
    <table style="width: 100%; margin-bottom: 18px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 50%; font-weight: bold;">&nbsp;</td>
            <td style="width: 2%;"></td>
            <td style="width: 25%; font-weight: bold; text-align: right;">
                Rp {{ number_format($gaji->gaji_bersih,0,',','.') }}
            </td>
        </tr>
    </table>

    <div class="section-space"></div>
    <table style="width:100%;">
        <tr>
            <td style="width:65%; vertical-align:top;">
                <div>Keterangan:</div>
                <div style="margin-bottom:8px;">Kutipan ini dibuat untuk:<br>Memenuhi persyaratan administratif</div>
            </td>
            <td style="width:35%; text-align:center;">
                Palembang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Bendahara Pengeluaran,<br><br><br><br>
                <strong>Nurlaila, SE, MM</strong><br>
                Pembina (IV/a)<br>
                NIP. 19761228 200003 2 002
            </td>
        </tr>
    </table>
</body>
</html>
