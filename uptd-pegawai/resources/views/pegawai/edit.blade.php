@extends('layouts.mantis')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Data Pegawai</h1>
        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengubah pegawai ini?')" id="pegawai-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Nama -->
            <div class="col-md-6 mb-3">
                <label for="nama" class="form-label @error('nama') text-danger @enderror">Nama</label>
                <input type="text" name="nama" id="nama"
                       class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama', $pegawai->nama) }}" autofocus>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- NIP -->
            <div class="col-md-6 mb-3">
                <label for="nip" class="form-label @error('nip') text-danger @enderror">Nomor Pegawai (NIP)</label>
                <input type="text" name="nip" id="nip"
                       class="form-control @error('nip') is-invalid @enderror"
                       value="{{ old('nip', $pegawai->nip) }}"
                       maxlength="20"
                       placeholder="Contoh: 123456789012345678 atau 001-BLUD-Labling">
                @error('nip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Jabatan -->
            <div class="col-md-6 mb-3">
                <label for="jabatan" class="form-label @error('jabatan') text-danger @enderror">Jabatan</label>
                <select name="jabatan" id="jabatan"
                        class="form-select @error('jabatan') is-invalid @enderror">
                    <option value="">-- Pilih Jabatan --</option>
                    <option value="Kepala UPTD" {{ old('jabatan', $pegawai->jabatan) == 'Kepala UPTD' ? 'selected' : '' }}>Kepala UPTD</option>
                    <option value="Kasi Pengendalian Mutu" {{ old('jabatan', $pegawai->jabatan) == 'Kasi Pengendalian Mutu' ? 'selected' : '' }}>Kasi Pengendalian Mutu</option>
                    <option value="Teknis" {{ old('jabatan', $pegawai->jabatan) == 'Teknis' ? 'selected' : '' }}>Teknis</option>
                    <option value="Tata Usaha" {{ old('jabatan', $pegawai->jabatan) == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                    <option value="Teknologi Informasi" {{ old('jabatan', $pegawai->jabatan) == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
                </select>
                @error('jabatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Gaji Pokok -->
            <div class="col-md-6 mb-3">
                <label for="gaji_pokok" class="form-label @error('gaji_pokok') text-danger @enderror">Gaji Pokok</label>
                <input type="text" name="gaji_pokok" id="gaji_pokok" autocomplete="off"
                       class="form-control @error('gaji_pokok') is-invalid @enderror">
                @error('gaji_pokok')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Insentif Kotor -->
            <div class="col-md-6 mb-3">
                <label for="insentif_kotor" class="form-label @error('insentif_kotor') text-danger @enderror">Insentif Kotor</label>
                <input type="text" name="insentif_kotor" id="insentif_kotor" autocomplete="off"
                       class="form-control @error('insentif_kotor') is-invalid @enderror">
                @error('insentif_kotor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Foto -->
            <div class="col-md-6 mb-3">
                <label for="foto" class="form-label @error('foto') text-danger @enderror">Foto (opsional)</label>
                <input type="file" name="foto" id="foto"
                       class="form-control @error('foto') is-invalid @enderror"
                       accept="image/*">
                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if($pegawai->foto)
                    <div class="mt-2">
                        <img src="{{ asset('foto_pegawai/'.$pegawai->foto) }}" alt="Foto Pegawai" style="max-height: 120px;">
                        <div class="small text-muted">Foto saat ini</div>
                    </div>
                @endif
            </div>
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

        // Set nilai awal dari database atau old()
        gaji.set('{{ old('gaji_pokok', $pegawai->gaji_pokok) }}');
        insentif.set('{{ old('insentif_kotor', $pegawai->insentif_kotor) }}');
    });
</script>
@endsection
