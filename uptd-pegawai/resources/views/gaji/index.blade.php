@extends('layouts.mantis')

@section('content')
    <div class="">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-3 border">
            <h4 class="mb-0">
                <i class="bi bi-wallet2 me-2"></i> Data Gaji Pegawai
            </h4>
            {{-- Form Filter --}}
            <form class="d-flex" method="GET" action="{{ route('gaji.index') }}">
                <select
                    class="form-select form-select-lg me-2 select2-pegawai"
                    name="pegawai_id"
                    style="min-width:260px;max-width:350px;width:350px;">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($pegawaiList as $pegawai)
                        <option value="{{ $pegawai->id }}" {{ request('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                            {{ $pegawai->nama }}{{ $pegawai->nip ? ' (' . $pegawai->nip . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select form-select-lg me-2" name="tahun" required>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </form>
        </div>

        {{-- Info Pegawai dan Tahun --}}
        @if (isset($selectedPegawai) && isset($selectedTahun))
            <div class="card shadow-sm mb-3 border-0">
                <div class="card-body py-3 px-4 position-relative" style="background:#f8fafd;">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-md-start text-center mb-2 mb-md-0">
                            <div class="fw-bold" style="font-size:1.5rem;">
                                Nama Pegawai: {{ $selectedPegawai->nama }}
                            </div>
                            <div class="text-secondary" style="font-size:1.1rem;">
                                NIP: {{ $selectedPegawai->nip ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
                            <span class="fw-bold text-info" style="font-size:2.2rem;">
                                Tahun {{ $selectedTahun }}
                            </span>
                        </div>
                        <div class="col-md-5"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tampilkan alert jika tidak ada data pegawai yang dipilih --}}
        @if (empty($bulanList) && request()->filled(['pegawai_id', 'tahun']))
            <div class="alert alert-info">Tidak ada data gaji untuk pegawai/tahun ini.</div>
        @endif

        {{-- Table --}}
        @if (isset($bulanList) && count($bulanList) > 0)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="datatable-gaji">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Bulan</th>
                                    <th>Total Gaji</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bulanList as $index => $bulan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $bulan['nama'] }}</td>
                                        <td>
                                            @if ($bulan['is_imported'])
                                                {{ format_rupiah($bulan['gaji_total']) }}
                                            @else
                                                <span class="text-warning">Belum import gaji</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($bulan['is_imported'] && isset($selectedPegawai))
                                                <a href="{{ route('gaji.preview', [
                                                    'pegawai_id' => $selectedPegawai->id,
                                                    'bulan' => $bulan['nomor'],
                                                    'tahun' => $selectedTahun,
                                                ]) }}"
                                                    class="btn btn-info btn-sm" title="Lihat">
                                                    <i class="bi bi-eye-fill"></i> Lihat
                                                </a>
                                                <a href="{{ route('gaji.review', ['pegawai_id' => $selectedPegawai->id, 'bulan' => $bulan['nomor'], 'tahun' => $selectedTahun]) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i> Edit
                                                </a>
                                                <form action="{{ route('gaji.hapus') }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin hapus data gaji bulan {{ $bulan['nama'] }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="pegawai_id"
                                                        value="{{ $selectedPegawai->id }}">
                                                    <input type="hidden" name="bulan" value="{{ $bulan['nomor'] }}">
                                                    <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="bi bi-trash-fill"></i> Hapus
                                                    </button>
                                                </form>
                                                {{-- Tombol Print --}}
                                                <a href="{{ route('gaji.payroll_pdf', [
                                                    'pegawai_id' => $selectedPegawai->id,
                                                    'bulan' => $bulan['nomor'],
                                                    'tahun' => $selectedTahun,
                                                ]) }}" target="_blank" class="btn btn-secondary btn-sm" title="Print">
                                                    <i class="bi bi-printer-fill"></i> Print
                                                </a>
                                            @elseif (!$bulan['is_imported'] && isset($selectedPegawai))
                                                <form action="{{ route('absen.import') }}" method="POST"
                                                    enctype="multipart/form-data" style="display:inline;"
                                                    id="form-import-{{ $bulan['nomor'] }}-{{ $selectedPegawai->id }}-{{ $selectedTahun }}">
                                                    @csrf
                                                    <input type="hidden" name="pegawai_id"
                                                        value="{{ $selectedPegawai->id }}">
                                                    <input type="hidden" name="bulan" value="{{ $bulan['nomor'] }}">
                                                    <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                                                    <input type="file" name="file_absen" accept=".xlsx,.xls"
                                                        style="display:none;"
                                                        onchange="document.getElementById('form-import-{{ $bulan['nomor'] }}-{{ $selectedPegawai->id }}-{{ $selectedTahun }}').submit();"
                                                        required>
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="this.previousElementSibling.click();">
                                                        Import Excel
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    {{-- Select2 CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Agar Select2 match dengan form-select-lg Bootstrap */
        .select2-container .select2-selection--single {
            height: calc(2.875rem + 2px) !important;
            padding: 0.5rem 1rem !important;
            font-size: 1.25rem !important;
            line-height: 1.5 !important;
            min-width: 220px !important;
            max-width: 350px !important;
            width: 350px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.375rem !important;
            font-size: 1.25rem !important;
            min-width: 220px !important;
            max-width: 330px !important;
            width: auto !important;
            white-space: normal !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.875rem !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.select2-pegawai').select2({
                width: 'style',
                placeholder: '-- Pilih Pegawai --',
                allowClear: true
            });
        });
    </script>
@endsection
