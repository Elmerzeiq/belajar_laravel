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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawai as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>{{ $item->jabatan }}</td>
                            <td>{{ format_rupiah($item->gaji_pokok) }}</td>
                            <td>{{ format_rupiah($item->insentif_kotor) }}</td>
                            <td class="text-center">
                                {{-- Edit button --}}
                                <a href="{{ route('pegawai.edit', $item->id) }}" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>

                                {{-- Delete form --}}
                                <form action="{{ route('pegawai.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus pegawai {{ $item->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pegawai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
