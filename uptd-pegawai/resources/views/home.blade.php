@extends('layouts.mantis')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="row g-4 mb-4">
                {{-- Jumlah Pegawai --}}
                <div class="col-md-6">
                    <div class="card shadow border-0 h-100" style="background: linear-gradient(90deg, #4F8EF7 0%, #68C7FF 100%);">
                        <div class="card-body">
                            <div class="mb-4">
                                <span class="fw-bold fs-5 text-white" style="display:block;">Jumlah Pegawai</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bi bi-people-fill text-white" style="font-size:2.5rem"></i>
                                </div>
                                <div>
                                    <span class="fs-1 fw-bold text-white">{{ $jumlahPegawai }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Pegawai Belum Diinput Gaji Bulan Ini --}}
                <div class="col-md-6">
                    <div class="card shadow border-0 h-100" style="background: linear-gradient(90deg, #F7971E 0%, #FFD200 100%);">
                        <div class="card-body">
                            <div class="mb-4">
                                <span class="fw-bold fs-5 text-white" style="display:block;">Pegawai Belum Diinput Gaji Bulan Ini</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bi bi-exclamation-triangle-fill text-white" style="font-size:2.5rem"></i>
                                </div>
                                <div>
                                    <span class="fs-1 fw-bold text-white">{{ $jumlahBelumGaji }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
