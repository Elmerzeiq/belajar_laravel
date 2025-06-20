@extends('layouts.mantis')

@section('content')
    <div class="container mt-4">
        <h4>Preview Data Absen & Potongan Insentif</h4>
        <div class="mb-2">
            <strong>Pegawai:</strong> {{ $pegawai_nama ?? 'Nama Pegawai' }} |
            <strong>NIP:</strong> {{ $pegawai_nip ?? 'NIP Pegawai' }} |
            <strong>Bulan:</strong> {{ $bulan }} |
            <strong>Tahun:</strong> {{ $tahun }}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Scan Masuk</th>
                        <th>Scan Keluar</th>
                        <th>Terlambat (mnt)</th>
                        <th>Pulang Cepat (mnt)</th>
                        <th>Potongan (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>{{ $row['hari'] }}</td>
                            <td>{{ $row['jam_masuk'] }}</td>
                            <td>{{ $row['jam_pulang'] }}</td>
                            <td>{{ $row['scan_masuk'] ?? '-' }}</td>
                            <td>{{ $row['scan_keluar'] ?? '-' }}</td>
                            <td>{{ $row['terlambat_menit'] }}</td>
                            <td>{{ $row['pulang_cepat_menit'] }}</td>
                            <td>{{ $row['potongan_persen'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3">
            <strong>Total Potongan Insentif:</strong> {{ $total_potongan_persen }}%
        </div>

        <form action="{{ route('gaji.review') }}" method="POST">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawai_id }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="total_potongan_persen" value="{{ $total_potongan_persen }}">
            <button type="submit" class="btn btn-success">
                Lanjutkan ke Review Gaji
            </button>
        </form>
    </div>
@endsection
