@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Bar Judul Data Pegawai --}}
    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-3 border">
        <h4 class="mb-0">
            <i class="bi bi-file-earmark-text-fill me-2"></i> Data Pegawai
        </h4>
        <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Pegawai
        </a>
    </div>

    {{-- Tabel Pegawai --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="myTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Gaji Pokok</th>
                            <th>Insentif Kotor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawai as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>{{ $item->jabatan }}</td>
                            <td>{{ format_rupiah($item->gaji_pokok) }}</td>
                            <td>{{ format_rupiah($item->insentif_kotor) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
