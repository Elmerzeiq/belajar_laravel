@extends('layouts.mantis')

@section('content')
<div class="container">
    <h4 class="mb-3">{{ isset($potongan_tetap) ? 'Edit Potongan' : 'Tambah Potongan' }}</h4>

    <form action="{{ isset($potongan_tetap) ? route('potongan-tetap.update', $potongan_tetap->id) : route('potongan-tetap.store') }}" method="POST">
        @csrf
        @if (isset($potongan_tetap))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label">Nama Potongan</label>
            <input type="text" name="nama_potongan" class="form-control" required value="{{ old('nama_potongan', $potongan_tetap->nama_potongan ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Potongan</label>
            <select name="tipe" class="form-select" onchange="toggleSatuan(this)" required>
                <option value="tetap" {{ (old('tipe', $potongan_tetap->tipe ?? '') == 'tetap') ? 'selected' : '' }}>Tetap (Rp)</option>
                <option value="persen" {{ (old('tipe', $potongan_tetap->tipe ?? '') == 'persen') ? 'selected' : '' }}>Persen (%)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Potongan</label>
            <div class="input-group">
                <span class="input-group-text satuan">{{ (old('tipe', $potongan_tetap->tipe ?? '') == 'persen') ? '%' : 'Rp' }}</span>
                <input type="number" name="jumlah" step="0.01" class="form-control" required value="{{ old('jumlah', $potongan_tetap->jumlah ?? '') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('potongan-tetap.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    function toggleSatuan(select) {
        const satuan = document.querySelector('.satuan');
        satuan.textContent = select.value === 'persen' ? '%' : 'Rp';
    }
</script>
@endsection
