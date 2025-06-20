<div>
    <!-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca -->
    <nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="../dashboard/index.html" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <span>Aplikasi Penggajian</span>
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <x-sidebar.links title="Home" route="home" icon="ti ti-home"></x-sidebar.links>
        <x-sidebar.links title="Data Pegawai" route="pegawai.index" icon="ti ti-users"></x-sidebar.links>
        <x-sidebar.links title="Potongan Tetap" route="potongan-tetap.index" icon="ti ti-credit-card-off"></x-sidebar.links>
        {{-- <x-sidebar.links title="Absensi" route="absensi.import" icon="ti ti-user-check"></x-sidebar.links> --}}
        {{-- <x-sidebar.links title="Rekap Absensi" route="rekap.gaji" icon="ti ti-clipboard"></x-sidebar.links> --}}
        <x-sidebar.links title="Gaji" route="gaji.index" icon="ti ti-wallet"></x-sidebar.links>
          </ul>
        </li>
    </div>
  </div>
</nav>
</div>
