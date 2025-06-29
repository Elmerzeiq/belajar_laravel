@extends('layouts.mantis')
@yield('scripts')

@section('content')
    <div class="container py-4">
        <h4 class="mb-4"><i class="bi bi-calculator"></i> Review Perhitungan Gaji</h4>

        <form action="{{ route('gaji.simpan') }}" method="POST" id="form-review-gaji" class="p-4 bg-white shadow-sm rounded-3">
            @csrf
            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            {{-- Info Pegawai --}}
            <div class="mb-4">
                <h5 class="fw-semibold">{{ $pegawai->nama }} <small class="text-muted">({{ $pegawai->nip ?? '-' }})</small></h5>
                <div class="text-muted">Periode: <strong>{{ bulanIndo($bulan) }} {{ $tahun }}</strong></div>
            </div>

            {{-- Input Gaji Pokok dan Insentif --}}
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                    <input type="text" name="gaji_pokok" id="gaji_pokok" class="form-control" required
                        value="{{ old('gaji_pokok', $gajiPokok) }}">
                </div>
                <div class="col-md-6">
                    <label for="insentif_tetap" class="form-label">Insentif Tetap</label>
                    <input type="text" name="insentif_tetap" id="insentif_tetap" class="form-control" required
                        value="{{ old('insentif_tetap', $insentifTetap) }}">
                </div>
            </div>

            {{-- Potongan Tetap --}}
            <div class="mb-4">
                <h5 class="fw-semibold">Potongan Tetap</h5>
                @foreach ($potongan_tetap as $index => $item)
                    <div class="row align-items-center mb-2">
                        <div class="col-md-1">
                            <input type="checkbox" class="form-check-input potongan-tetap-checkbox"
                                id="potongan_tetap_check_{{ $item->id }}" data-index="{{ $index }}" checked>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="potongan_tetap_check_{{ $item->id }}">
                                {{ $item->nama_potongan }}
                                <small class="text-muted">({{ ucfirst($item->tipe) }})</small>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="potongan_tetap[{{ $item->id }}]" class="form-control potongan-tetap-input"
                                value="{{ old('potongan_tetap.' . $item->id, (int) $item->jumlah) }}"
                                data-default="{{ old('potongan_tetap.' . $item->id, (int) $item->jumlah) }}"
                                data-tipe="{{ $item->tipe }}" data-jenis="{{ $item->jenis_potongan }}" required>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Potongan Lain dan Bonus --}}
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <label for="potongan_lain" class="form-label">Potongan Lain (jika ada)</label>
                    <input type="text" name="potongan_lain" id="potongan_lain" class="form-control" value="0">
                </div>
                <div class="col-md-6">
                    <label for="bonus" class="form-label">Bonus (jika ada)</label>
                    <input type="text" name="bonus" id="bonus" class="form-control" value="0">
                </div>
            </div>

            {{-- Potongan Insentif Import dan Nominalnya --}}
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <label for="potongan_insentif_import" class="form-label">Potongan Insentif Hasil Import (%)</label>
                    <input type="text" name="potongan_insentif_import" id="potongan_insentif_import" class="form-control"
                        value="{{ old('potongan_insentif_import', $total_potongan_insentif_import ?? 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="insentif_import" class="form-label">Nominal Potongan Insentif Import</label>
                    <input type="text" name="insentif_import" id="insentif_import" class="form-control" value="0" required
                        readonly>
                </div>
            </div>

            {{-- Total Gaji --}}
            <div class="alert alert-primary fs-5 rounded-2 py-2 px-3 mb-4">
                <strong>Total Gaji Bersih:</strong> <span id="total_gaji_display">Rp 0</span>
            </div>

            {{-- Tombol Kembali dan Simpan --}}
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('gaji.index', ['pegawai_id' => $pegawai->id, 'tahun' => $tahun]) }}"
                    class="btn btn-outline-primary">
                    ← Kembali
                </a>

                <button type="submit" class="btn btn-success px-4"
                    onclick="return confirm('Yakin ingin menyimpan gaji ini?')">
                    <i class="bi bi-save me-1"></i> Simpan Gaji
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const autoNumericOptions = {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 1,
                minimumValue: '0',
                unformatOnSubmit: true
            };

            // Inisialisasi AutoNumeric
            const fields = [
                new AutoNumeric('#gaji_pokok', autoNumericOptions),
                new AutoNumeric('#insentif_tetap', autoNumericOptions),
                new AutoNumeric('#potongan_lain', autoNumericOptions),
                new AutoNumeric('#bonus', autoNumericOptions),
                new AutoNumeric('#potongan_insentif_import', Object.assign({}, autoNumericOptions, { maximumValue: '100', minimumValue: '0' })),
            ];

            const potonganInputs = [];
            document.querySelectorAll('.potongan-tetap-input').forEach(input => {
                potonganInputs.push(new AutoNumeric(input, autoNumericOptions));
            });

            // Restore/reset potongan tetap value
            document.querySelectorAll('.potongan-tetap-checkbox').forEach((checkbox, idx) => {
                checkbox.addEventListener('change', function() {
                    const input = this.closest('.row').querySelector('.potongan-tetap-input');
                    // Kembalikan ke value default
                    if (this.checked) {
                        input.disabled = false;
                        let defaultValue = input.getAttribute('data-default') || 0;
                        potonganInputs[idx].set(defaultValue);
                    } else {
                        input.disabled = true;
                        potonganInputs[idx].set(0);
                    }
                    updateTotal();
                });
            });
            // Potongan insentif import: editable, nominal otomatis
            function updateNominalInsentifImport() {
                const insentifTetap = fields[1].getNumber();
                const persen = parseFloat(document.getElementById('potongan_insentif_import').value.replace(',', '.')) || 0;
                const nominal = Math.floor(insentifTetap * persen / 100);
                const insentifImportInput = document.getElementById('insentif_import');
                insentifImportInput.value = nominal;
                // Juga update format tampilannya
                if (window.AutoNumeric && insentifImportInput.autoNumeric) {
                    insentifImportInput.autoNumeric.set(nominal);
                }
            }

            // Inisialisasi Nominal Insentif Import pertama kali
            function initNominalInsentifImport() {
                const insentifImportInput = document.getElementById('insentif_import');
                if (window.AutoNumeric) {
                    insentifImportInput.autoNumeric = new AutoNumeric(insentifImportInput, autoNumericOptions);
                }
                updateNominalInsentifImport();
            }
// Update total gaji
            function getNumericValue(anInstance) {
                return anInstance.getNumber();
            }

            function updateTotal() {
                let gajiPokok = getNumericValue(fields[0]);
                let insentif = getNumericValue(fields[1]);

                // Potongan insentif hasil import
                let potonganPersenImport = parseFloat(document.getElementById('potongan_insentif_import').value.replace(',', '.')) || 0;
                let insentifSetelahPotonganImport = insentif - (insentif * potonganPersenImport / 100);

                let potonganLain = getNumericValue(fields[2]);
                let bonus = getNumericValue(fields[3]);
// Inisialisasi akumulasi potongan
                let potonganGaji = 0;
                let potonganInsentif = 0;
                let potonganTotal = 0;
                let totalPersenPotonganTotal = [];

                document.querySelectorAll('.potongan-tetap-input').forEach((input, i) => {
                    if (!input.disabled) {
                        let nilai = potonganInputs[i].getNumber();
                        let tipe = input.getAttribute('data-tipe');
                        let jenis = input.getAttribute('data-jenis');
                        if (tipe === 'persen') {
                            if (jenis === 'gaji_pokok') {
                                potonganGaji += gajiPokok * nilai / 100;
                            } else if (jenis === 'insentif') {
                                potonganInsentif += insentif * nilai / 100;
                            } else if (jenis === 'total') {
                                totalPersenPotonganTotal.push(nilai);
                            }
                        } else {
                            if (jenis === 'gaji_pokok') {
                                potonganGaji += nilai;
                            } else if (jenis === 'insentif') {
                                potonganInsentif += nilai;
                            } else if (jenis === 'total') {
                                potonganTotal += nilai;
                            }
                        }
                    }
                });
// Gaji dan insentif setelah potongan masing-masing
                let gajiSetelahPotongan = gajiPokok - potonganGaji;
                let insentifSetelahPotongan = insentifSetelahPotonganImport - potonganInsentif;
// Potongan total (persen): dihitung setelah potongan gaji dan insentif
                let totalSementara = gajiSetelahPotongan + insentifSetelahPotongan;
                totalPersenPotonganTotal.forEach(nilai => {
                    potonganTotal += totalSementara * nilai / 100;
                });

                let total = gajiSetelahPotongan + insentifSetelahPotongan + bonus - potonganTotal - potonganLain;
                if (total < 0) total = 0;

                document.getElementById('total_gaji_display').textContent =
                    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(total);
// Update nominal insentif import setiap kali update total (karena insentif atau persen bisa berubah)
                updateNominalInsentifImport();
            }
// Listener untuk Potongan Insentif Import (editable)
            document.getElementById('potongan_insentif_import').addEventListener('input', function() {
                updateNominalInsentifImport();
                updateTotal();
            });
// Listener untuk Insentif Tetap (karena jika berubah, nominal insentif import juga berubah)
            document.getElementById('insentif_tetap').addEventListener('input', function() {
                updateNominalInsentifImport();
                updateTotal();
            });
// Listeners lain
            [...fields, ...potonganInputs].forEach(an => {
                an.domElement.addEventListener('input', updateTotal);
            });

            initNominalInsentifImport();
            updateTotal();
        });
    </script>
@endsection
