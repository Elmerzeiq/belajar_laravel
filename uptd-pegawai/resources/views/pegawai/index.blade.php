@extends('layouts.app')

@section('content')
<div class="container mt-5"> <!-- Tambahkan margin-top di sini -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Data Pegawai</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Pegawai</th>
                        <th scope="col">Jabatan</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Tanggal Lahir</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawai as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->jabatan }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}
                            </td>
                            <td>
                                <!-- Isi aksi nanti jika perlu -->
                                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
