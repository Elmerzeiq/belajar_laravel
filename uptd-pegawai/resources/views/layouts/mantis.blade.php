<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Aplikasi Penggajian</title>
    <!-- [Meta] -->
    <x-meta></x-meta>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<!-- [Head] end -->
<!-- [Body] Start -->
@stack('scripts')
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <x-sidebar></x-sidebar>
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
    <x-header></x-header>
    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <x-breadcrumbs></x-breadcrumbs>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>

        </div>
    </div>
    <!-- [ Main Content ] end -->
    <x-footer></x-footer>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // kolom terakhir (Aksi) tidak bisa diurutkan
                ],
                "columns": [
                    null, // No
                    null, // Nama
                    null, // NIP
                    null, // Jabatan
                    null, // Gaji Pokok
                    null, // Insentif Kotor
                    null, // Status
                    null, // Foto
                    null // Aksi (disable sort kalau mau)
                ]
            });
        });
    </script>


    <!-- [Page Specific JS] start -->
    <script src="{{ asset('template/dist') }}/assets/js/plugins/apexcharts.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/pages/dashboard-default.js"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="{{ asset('template/dist') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/pcoded.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/plugins/feather.min.js"></script>





    <script>
        layout_change('light');
    </script>




    <script>
        change_box_container('false');
    </script>



    <script>
        layout_rtl_change('false');
    </script>


    <script>
        preset_change("preset-1");
    </script>


    <script>
        font_change("Public-Sans");
    </script>


    @yield('scripts')
</body>
<!-- [Body] end -->

</html>
