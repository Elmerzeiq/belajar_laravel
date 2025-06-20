@extends('layouts.mantis')

@section('content')
<div class="">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-3 border">
        <h4 class="mb-0">
            <i class="bi bi-scissors me-2"></i> Data Potongan Tetap
        </h4>
        <a href="{{ route('potongan-tetap.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Potongan
        </a>
    </div>

    {{-- Search & Entries --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <label>
                Tampilkan
                <select id="entries-select" class="form-select d-inline-block w-auto mx-1">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">Semua</option>
                </select>
                entri
            </label>
        </div>
        <div>
            <input type="text" id="search-input" class="form-control" placeholder="Cari potongan tetap...">
        </div>
    </div>

    {{-- Tampilkan alert jika tidak ada data --}}
    @if($potongan->isEmpty())
        <div class="alert alert-info">Belum ada potongan tetap.</div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="potongan-table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Potongan</th>
                            <th>Jenis Potongan</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="potongan-tbody">
                        @foreach ($potongan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_potongan }}</td>
                                <td>
                                    @if ($item->jenis_potongan == 'gaji_pokok')
                                        Gaji Pokok
                                    @elseif ($item->jenis_potongan == 'insentif')
                                        Insentif
                                    @else
                                        Total
                                    @endif
                                </td>
                                <td>{{ ucfirst($item->tipe) }}</td>
                                <td>{{ $item->tipe == 'persen' ? $item->jumlah . '%' : format_rupiah($item->jumlah) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('potongan-tetap.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <form action="{{ route('potongan-tetap.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus potongan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pesan jika hasil pencarian kosong --}}
                <div id="no-result" class="alert alert-warning text-center d-none mt-3">Tidak ditemukan data yang sesuai.</div>
            </div>

            {{-- Pagination --}}
            <div id="pagination" class="mt-3 d-flex justify-content-end"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const entriesSelect = document.getElementById('entries-select');
        const tbody = document.getElementById('potongan-tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const noResult = document.getElementById('no-result');
        const pagination = document.getElementById('pagination');

        let filteredRows = [...rows];
        let currentPage = 1;
        let entriesPerPage = parseInt(entriesSelect.value);

        function renderTable() {
            tbody.innerHTML = '';
            const start = (currentPage - 1) * entriesPerPage;
            const end = entriesPerPage === -1 ? filteredRows.length : start + entriesPerPage;
            const paginatedRows = filteredRows.slice(start, end);

            if (paginatedRows.length === 0) {
                noResult.classList.remove('d-none');
            } else {
                noResult.classList.add('d-none');
                paginatedRows.forEach(row => tbody.appendChild(row));
            }

            renderPagination();
        }

        function renderPagination() {
            pagination.innerHTML = '';
            if (entriesPerPage === -1 || filteredRows.length <= entriesPerPage) return;

            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
                btn.textContent = i;
                btn.onclick = () => {
                    currentPage = i;
                    renderTable();
                };
                pagination.appendChild(btn);
            }
        }

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            filteredRows = rows.filter(row => row.innerText.toLowerCase().includes(keyword));
            currentPage = 1;
            renderTable();
        });

        entriesSelect.addEventListener('change', function () {
            entriesPerPage = parseInt(this.value);
            currentPage = 1;
            renderTable();
        });

        renderTable();
    });
</script>
@endsection
