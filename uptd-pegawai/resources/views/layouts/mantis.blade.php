<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Aplikasi Penggajian</title>
    <!-- [Meta] -->
    <x-meta></x-meta>

    <!-- Bootstrap Icons (opsional) -->
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
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
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
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <x-footer></x-footer>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }],
                "columns": [null, null, null, null, null, null, null, null, null]
            });
        });
    </script>

    <!-- Page Specific JS -->
    <script src="{{ asset('template/dist/assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/pages/dashboard-default.js') }}"></script>

    <!-- Required JS -->
    <!-- GANTI ke bootstrap.bundle.min.js -->
    <script src="{{ asset('template/dist/assets/js/plugins/bootstrap.bundle.min.js') }}"></script>

    <!-- Plugin lainnya -->
    <script src="{{ asset('template/dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/plugins/feather.min.js') }}"></script>

    <!-- Inisialisasi dropdown (jaga-jaga) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownTriggerList.map(function(dropdownTriggerEl) {
                return new bootstrap.Dropdown(dropdownTriggerEl);
            });
        });
    </script>

    <!-- Preset Pengaturan Layout -->
    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>

    @yield('scripts')
</body>
<!-- [Body] end -->

</html>