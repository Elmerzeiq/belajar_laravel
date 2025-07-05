@extends('layouts.mantis')

@section('content')
    <div class="container">
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <h4 class="mb-3">
                    <i class="bi bi-journal-text me-2"></i> Laporan Gaji Pegawai
                </h4>

                <!-- Form dengan flex supaya semua elemen sejajar ke kiri -->
                <form class="d-flex align-items-end gap-2 mb-4" method="GET" action="{{ route('laporan.gaji') }}">
                    <!-- Input Bulan -->
                    <div class="d-flex flex-column">
                        <label>Bulan</label>
                        <select name="bulan" class="form-select form-select-sm select2-bulan bulan-input" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach ($bulanList as $num => $nama)
                                <option value="{{ $num }}"
                                    {{ (request('bulan') ?? date('n')) == $num ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Tahun -->
                    <div class="d-flex flex-column">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control form-control-sm tahun-input"
                            value="{{ request('tahun') ?? date('Y') }}" min="2000" max="2099" required>
                    </div>

                    <!-- Tombol Tampilkan -->
                    <div>
                        <button class="btn btn-primary" type="submit">Tampilkan</button>
                    </div>
                </form>

                <!-- Tampilkan tombol print jika data tersedia -->
                @if (request('bulan') && request('tahun') && count($laporan) > 0)
                    <a href="{{ route('laporan.gaji.print', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                        class="btn btn-secondary mb-3" target="_blank">
                        <i class="bi bi-printer-fill"></i> Print Laporan
                    </a>
                @endif

                <!-- Tampilkan keterangan bulan dan tahun yang dipilih -->
                @if (request('bulan') && request('tahun'))
                    <div class="mb-3">
                        <strong>Bulan:</strong> {{ $bulanList[request('bulan')] ?? '' }} &nbsp;
                        <strong>Tahun:</strong> {{ request('tahun') }}
                    </div>

                    <!-- Jika data laporan tersedia, tampilkan tabel -->
                    @if (count($laporan) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Nomor Pegawai</th>
                                        <th>Jabatan</th>
                                        <th style="text-align:right;">Total Gaji</th>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Jika tidak ada data -->
                        <div class="alert alert-info">Tidak ada data gaji yang diproses pada bulan dan tahun ini.</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Import CSS dan JS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 pada input bulan
            $('.select2-bulan').select2({
                width: 'resolve', // Lebar otomatis
                placeholder: '-- Pilih Bulan --',
                allowClear: true // Memungkinkan menghapus pilihan
            });
        });
    </script>

    <style>
        /* CSS untuk mengatur lebar input bulan */
        .bulan-input {
            width: 150px !important;
        }

        /* CSS untuk mengatur lebar input tahun */
        .tahun-input {
            width: 100px !important;
        }

        /* CSS untuk memperkecil tampilan Select2 agar sesuai dengan input kecil */
        .select2-container--default .select2-selection--single {
            height: 31px !important;
            padding: 4px 8px !important;
            font-size: 0.875rem !important; /* Ukuran font kecil */
        }

        /* Mengatur line-height text di Select2 agar rata tengah */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
        }

        /* Mengatur tinggi arrow dropdown di Select2 */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }
    </style>
@endsection
