@extends('layouts.mantis')

@section('content')
<div class="container">
    <h4 class="mb-3">{{ isset($potongan_tetap) ? 'Edit Potongan' : 'Tambah Potongan' }}</h4>

    <form
        action="{{ isset($potongan_tetap) ? route('potongan-tetap.update', $potongan_tetap->id) : route('potongan-tetap.store') }}"
        method="POST" id="potongan-form">
        @csrf
        @if (isset($potongan_tetap))
            @method('PUT')
        @endif

        {{-- Nama Potongan --}}
        <div class="mb-3">
            <label for="nama_potongan" class="form-label @error('nama_potongan') text-danger @enderror">Nama Potongan</label>
            <input type="text" name="nama_potongan" id="nama_potongan"
                class="form-control @error('nama_potongan') is-invalid @enderror"
                value="{{ old('nama_potongan', $potongan_tetap->nama_potongan ?? '') }}" autofocus>
            @error('nama_potongan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Jenis Potongan --}}
        <div class="mb-3">
            <label for="jenis_potongan" class="form-label">Jenis Potongan</label>
            <select name="jenis_potongan" id="jenis_potongan" class="form-select @error('jenis_potongan') is-invalid @enderror">
                <option value="gaji_pokok" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'gaji_pokok' ? 'selected' : '' }}>Gaji Pokok</option>
                <option value="insentif" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'insentif' ? 'selected' : '' }}>Insentif</option>
                <option value="total" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'total' ? 'selected' : '' }}>Total (Gaji Pokok + Insentif)</option>
            </select>
            @error('jenis_potongan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tipe Potongan --}}
        <div class="mb-3">
            <label for="tipe-potongan" class="form-label">Tipe Potongan</label>
            <select name="tipe" id="tipe-potongan" class="form-select @error('tipe') is-invalid @enderror"
                onchange="toggleTipePotongan(); toggleSatuan(this)">
                <option value="tetap" {{ old('tipe', $potongan_tetap->tipe ?? '') == 'tetap' ? 'selected' : '' }}>Tetap (Rp)</option>
                <option value="persen" {{ old('tipe', $potongan_tetap->tipe ?? '') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
            </select>
            @error('tipe')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Jumlah Potongan --}}
        <div class="mb-3">
            <label for="jumlah" class="form-label @error('jumlah') text-danger @enderror">Jumlah Potongan</label>
            <div class="input-group">
                <span class="input-group-text satuan">{{ old('tipe', $potongan_tetap->tipe ?? '') == 'persen' ? '%' : 'Rp' }}</span>
                <input type="text" name="jumlah" id="jumlah"
                    class="form-control @error('jumlah') is-invalid @enderror"
                    value="{{ old('jumlah', $potongan_tetap->jumlah ?? '') }}" autocomplete="off">
            </div>
            @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('potongan-tetap.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
<script>
    let jumlahInput;
    let autoNumericInstance;

    document.addEventListener('DOMContentLoaded', function () {
        jumlahInput = document.getElementById('jumlah');
        toggleTipePotongan();

        jumlahInput.addEventListener('input', function () {
            const tipe = document.getElementById('tipe-potongan').value;
            if (tipe === 'persen') {
                let val = parseFloat(jumlahInput.value);
                if (val > 100) jumlahInput.value = 100;
                if (val < 0) jumlahInput.value = 0;
            }
        });
    });

    function toggleTipePotongan() {
        const tipe = document.getElementById('tipe-potongan').value;
        let currentValue = jumlahInput.value;

        if (autoNumericInstance) {
            currentValue = autoNumericInstance.getNumber();
            autoNumericInstance.remove();
            autoNumericInstance = null;
        }

        if (tipe === 'tetap') {
            jumlahInput.type = 'text';
            autoNumericInstance = new AutoNumeric(jumlahInput, {
                currencySymbol: '',
                decimalPlaces: 0,
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                unformatOnSubmit: true,
                minimumValue: '0',
                maximumValue: '9999999999',
                modifyValueOnWheel: false,
            });
            if (currentValue) autoNumericInstance.set(currentValue);
        } else {
            jumlahInput.type = 'number';
            jumlahInput.step = '0.01';
            jumlahInput.min = '0';
            jumlahInput.max = '100';
            let num = parseFloat(currentValue);
            jumlahInput.value = isNaN(num) ? '' : Math.min(num, 100);
        }
    }

    function toggleSatuan(select) {
        const satuan = document.querySelector('.satuan');
        satuan.textContent = select.value === 'persen' ? '%' : 'Rp';
    }
</script>
@endsection
