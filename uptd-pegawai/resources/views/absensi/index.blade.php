@extends('layouts.mantis')

@section('content')
<div class="">
    <h3>Data Absensi</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('absensi.import.form') }}" class="btn btn-primary mb-3">Import Data Absensi</a>

    @if($absensis->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama Pegawai</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Potongan (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absensis as $index => $absensi)
                    <tr>
                        <td>{{ $absensis->firstItem() + $index }}</td>
                        <td>{{ $absensi->pegawai->nip }}</td>
                        <td>{{ $absensi->pegawai->nama }}</td>
                        <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $absensi->jam_masuk }}</td>
                        <td>{{ $absensi->jam_pulang }}</td>
                        <td>{{ $absensi->potongan_persen }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $absensis->links() }}

    @else
        <p>Tidak ada data absensi.</p>
    @endif
</div>
@endsection
