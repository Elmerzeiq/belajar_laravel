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

        <div class="mb-3">
            <label class="form-label">Nama Potongan</label>
            <input type="text" name="nama_potongan" class="form-control" required
                value="{{ old('nama_potongan', $potongan_tetap->nama_potongan ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Jenis Potongan</label>
            <select name="jenis_potongan" class="form-select" required>
                <option value="gaji_pokok" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'gaji_pokok' ? 'selected' : '' }}>Gaji Pokok</option>
                <option value="insentif" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'insentif' ? 'selected' : '' }}>Insentif</option>
                <option value="total" {{ old('jenis_potongan', $potongan_tetap->jenis_potongan ?? '') == 'total' ? 'selected' : '' }}>Total (Gaji Pokok + Insentif)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Potongan</label>
            <select name="tipe" id="tipe-potongan" class="form-select" onchange="toggleTipePotongan(); toggleSatuan(this)" required>
                <option value="tetap" {{ old('tipe', $potongan_tetap->tipe ?? '') == 'tetap' ? 'selected' : '' }}>Tetap (Rp)</option>
                <option value="persen" {{ old('tipe', $potongan_tetap->tipe ?? '') == 'persen' ? 'selected' : '' }}>Persen (%)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Potongan</label>
            <div class="input-group">
                <span class="input-group-text satuan">{{ old('tipe', $potongan_tetap->tipe ?? '') == 'persen' ? '%' : 'Rp' }}</span>
                <input type="text" name="jumlah" id="jumlah" class="form-control" required autocomplete="off"
                    value="{{ old('jumlah', $potongan_tetap->jumlah ?? '') }}">
            </div>
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

        // Validasi input maksimum 100 jika persen
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
