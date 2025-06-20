@extends('layouts.mantis')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Data Pegawai</h1>
        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah pegawai ini?')" id="pegawai-form">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama" class="form-label @error('nama') text-danger @enderror">Nama</label>
            <input
                type="text"
                name="nama"
                id="nama"
                class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama', $pegawai->nama) }}"
                autofocus
            >
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nip" class="form-label @error('nip') text-danger @enderror">NIP</label>
            <input
                type="number"
                name="nip"
                id="nip"
                class="form-control @error('nip') is-invalid @enderror"
                value="{{ old('nip', $pegawai->nip) }}"
            >
            @error('nip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jabatan" class="form-label @error('jabatan') text-danger @enderror">Jabatan</label>
            <input
                type="text"
                name="jabatan"
                id="jabatan"
                class="form-control @error('jabatan') is-invalid @enderror"
                value="{{ old('jabatan', $pegawai->jabatan) }}"
            >
            @error('jabatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="gaji_pokok" class="form-label @error('gaji_pokok') text-danger @enderror">Gaji Pokok</label>
            <input
                type="text"
                name="gaji_pokok"
                id="gaji_pokok"
                autocomplete="off"
                class="form-control @error('gaji_pokok') is-invalid @enderror"
            >
            @error('gaji_pokok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="insentif_kotor" class="form-label @error('insentif_kotor') text-danger @enderror">Insentif Kotor</label>
            <input
                type="text"
                name="insentif_kotor"
                id="insentif_kotor"
                autocomplete="off"
                class="form-control @error('insentif_kotor') is-invalid @enderror"
            >
            @error('insentif_kotor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const options = {
            currencySymbol: 'Rp ',
            decimalPlaces: 0,
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            currencySymbolPlacement: 'p',
            unformatOnSubmit: true,
            minimumValue: '0',
            maximumValue: '9999999999',
            modifyValueOnWheel: false,
        };

        // Inisialisasi AutoNumeric
        const gaji = new AutoNumeric('#gaji_pokok', options);
        const insentif = new AutoNumeric('#insentif_kotor', options);

        // Set nilai awal dari database (atau old() kalau ada error)
        gaji.set('{{ old('gaji_pokok', $pegawai->gaji_pokok) }}');
        insentif.set('{{ old('insentif_kotor', $pegawai->insentif_kotor) }}');

        // Blok input selain angka (hanya boleh angka)
        document.querySelectorAll('#gaji_pokok, #insentif_kotor').forEach(function(input) {
            input.addEventListener('keypress', function(e) {
                if (e.key.length === 1 && !/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
