@extends('layouts.mantis')

@section('content')
    <div class="container mt-4">

        {{-- Tombol Kembali --}}
        <div class="mb-3">
            <a href="{{ route('gaji.index', ['pegawai_id' => $pegawai->id, 'tahun' => $tahun]) }}"
                class="btn btn-outline-primary">
                ← Kembali
            </a>
        </div>

        {{-- Judul Tengah --}}
        <div class="text-center mb-4">
            <h3 class="fw-bold border-bottom pb-2 d-inline-block text-primary">
                Preview Data Absensi & Potongan Insentif
            </h3>
        </div>

        {{-- Header --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body py-3 px-4 position-relative bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-start">
                    {{-- Kiri: Nama Pegawai --}}
                    <div class="mb-2">
                        <div class="fw-bold" style="font-size:1.5rem;">
                            Nama Pegawai: {{ $pegawai->nama }}
                        </div>
                        <div class="text-secondary" style="font-size:1.1rem;">
                            NIP: {{ $pegawai->nip ?? '-' }}
                        </div>
                    </div>

                    {{-- Kanan: Bulan dan Tahun, posisinya kanan, tapi rata kiri --}}
                    <div class="ms-auto text-start" style="min-width:150px;">
                        <div class="text-dark fw-bold" style="font-size:1.1rem;">
                            Bulan: <span class="fw-normal">{{ bulanIndo($bulan) }}</span>
                        </div>
                        <div class="text-dark fw-bold" style="font-size:1.1rem;">
                            Tahun: <span class="fw-normal">{{ $tahun }}</span>
                        </div>
                    </div>
                </div>
            </div>
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

        {{-- Tombol Lanjut ke Review hanya muncul saat mode=edit --}}
        @if (request()->query('mode') !== 'lihat')
            <form action="{{ route('gaji.review') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="pegawai_id" value="{{ $pegawai_id }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="total_potongan_persen" value="{{ $total_potongan_persen }}">
                <button type="submit" class="btn btn-success">
                    Lanjutkan ke Review Gaji
                </button>
            </form>
        @endif
    </div>
@endsection
