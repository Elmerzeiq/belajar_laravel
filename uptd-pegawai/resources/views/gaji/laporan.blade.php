@extends('layouts.mantis')

@section('content')
    <div class="container">
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <h4 class="mb-3">
                    <i class="bi bi-journal-text me-2"></i> Laporan Gaji Pegawai
                </h4>
                <form class="row g-2 align-items-end mb-4" method="GET" action="{{ route('laporan.gaji') }}">
                    <div class="col-md-4">
                        <label>Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach ($bulanList as $num => $nama)
                                <option value="{{ $num }}"
                                    {{ (request('bulan') ?? date('n')) == $num ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Pemilihan tahun pada laporan --}}
                    <div class="col-md-4">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control"
                            value="{{ request('tahun') ?? date('Y') }}" min="2000" max="2099" required>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit">Tampilkan</button>
                    </div>
                </form>

                @if (request('bulan') && request('tahun') && count($laporan) > 0)
                    <a href="{{ route('laporan.gaji.print', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                        class="btn btn-secondary mb-3" target="_blank">
                        <i class="bi bi-printer-fill"></i> Print Laporan
                    </a>
                @endif

                @if (request('bulan') && request('tahun'))
                    <div class="mb-3">
                        <strong>Bulan:</strong> {{ $bulanList[request('bulan')] ?? '' }} &nbsp;
                        <strong>Tahun:</strong> {{ request('tahun') }}
                    </div>
                    @if (count($laporan) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Nomor Pegawai</th>
                                        <th>Jabatan</th>
                                        <th>Total Gaji</th>
                                        {{-- Tambahkan kolom lain sesuai kebutuhan --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($laporan as $index => $gaji)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $gaji->pegawai->nama ?? '-' }}</td>
                                            <td>{{ $gaji->pegawai->nip ?? '-' }}</td>
                                            <td>{{ $gaji->pegawai->jabatan ?? '-' }}</td>
                                            <td style="text-align:right;">
                                                {{ number_format($gaji->gaji_bersih ?? 0, 0, ',', '.') }}
                                            </td>
                                            {{-- Kolom lain jika ada --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">Tidak ada data gaji yang diproses pada bulan dan tahun ini.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
