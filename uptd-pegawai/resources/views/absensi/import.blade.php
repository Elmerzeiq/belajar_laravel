@extends('layouts.mantis')

@section('content')
<div class="">
    <h3>Import Data Absensi</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file_absensi">Pilih file Excel (.xlsx/.xls)</label>
            <input type="file" name="file_absensi" id="file_absensi" class="form-control" required>
            @error('file_absensi')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-2">Upload</button>
    </form>
</div>
@endsection
