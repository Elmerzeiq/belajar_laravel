@extends('layouts.mantis')

@section('content')
<div class="container">
    <h4 class="mb-3">Data Potongan Tetap</h4>
    <a href="{{ route('potongan-tetap.create') }}" class="btn btn-primary mb-3">Tambah Potongan</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Potongan</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($potongan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_potongan }}</td>
                    <td>{{ ucfirst($item->tipe) }}</td>
                    <td>{{ $item->tipe == 'persen' ? $item->jumlah . '%' : format_rupiah($item->jumlah) }}</td>
                    <td>
                        <a href="{{ route('potongan-tetap.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('potongan-tetap.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus potongan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada potongan tetap.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
