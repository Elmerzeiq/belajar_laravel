@extends('layouts.mantis')

@section('content')
    <div class="">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-3 border">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-text-fill me-2"></i> Data Pegawai
            </h4>
            <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Pegawai
            </a>
        </div>

        {{-- Alert jika data kosong --}}
        @if ($pegawai->count() == 0)
            <div class="alert alert-warning text-center">
                Tidak ada data pegawai.
            </div>
        @endif

        {{-- Alert jika menghapus data pegawai yang masih ada data gajinya --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Gaji Pokok</th>
                                <th>Insentif Kotor</th>
                                <th>Status</th>
                                <th>Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawai as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->nip }}</td>
                                    <td>{{ $item->jabatan }}</td>
                                    <td>{{ format_rupiah($item->gaji_pokok) }}</td>
                                    <td>{{ format_rupiah($item->insentif_kotor) }}</td>
                                    <td>
                                        @if ($item->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->foto)
                                            <img src="{{ asset('foto_pegawai/' . $item->foto) }}" alt="Foto Pegawai"
                                                style="max-width: 55px; max-height: 55px; border-radius: 6px;">
                                        @else
                                            <img src="{{ asset('img/nouser.jpg') }}" alt="Default Foto"
                                                style="max-width: 55px; max-height: 55px; border-radius: 6px;">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <a href="{{ route('pegawai.edit', $item->id) }}" class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="bi bi-pencil-fill"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
