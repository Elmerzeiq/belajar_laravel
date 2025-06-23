<nav class="pc-sidebar" style="min-width: 220px;">
    <div class="navbar-wrapper">
        <div class="m-header">
            <div class="b-brand text-primary d-flex flex-column align-items-center">
                <img src="{{ asset('img/logouptd.png') }}" alt="Logo UPTD">
                <span style="font-size:1.18rem; font-weight:700; letter-spacing:0.2px; text-align:center; margin-top:8px; margin-bottom:30px;">
                    Aplikasi Penggajian
                </span>
            </div>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar" style="width: 100%;">
                <x-sidebar.links title="Home" route="home" icon="ti ti-home"></x-sidebar.links>
                <x-sidebar.links title="Data Pegawai" route="pegawai.index" icon="ti ti-users"></x-sidebar.links>
                <x-sidebar.links title="Potongan Tetap" route="potongan-tetap.index" icon="ti ti-credit-card-off"></x-sidebar.links>
                <x-sidebar.links title="Gaji" route="gaji.index" icon="ti ti-wallet"></x-sidebar.links>
                <x-sidebar.links title="Laporan Gaji" route="laporan.gaji" icon="ti ti-report" />
            </ul>
        </div>
    </div>
</nav>

<style>
.pc-sidebar {
    min-width: 220px;
    background: #fff !important;
    border-right: 1px solid #eee;
}
.m-header {
    min-height: 140px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding-top: 24px;
    padding-bottom: 10px;
    overflow: visible;
}
.b-brand img {
    height: 65px;
    width: auto;
    margin-bottom: 10px;
    display: block;
}
.b-brand span {
    display: block;
    text-align: center;
    color: #222;
    font-size: 1.18rem;
    font-weight: 700;
    letter-spacing: 0.2px;
    margin-bottom: 30px;
    margin-top: 8px;
}
/* Link styling seperti sebelumnya */
.pc-navbar .pc-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #222;
    border-radius: 7px;
    padding: 10px 18px;
    margin-bottom: 10px;
    font-size: 1rem;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
}
.pc-navbar .pc-link.active {
    background: #2196f3 !important;
    color: #fff !important;
}
.pc-navbar .pc-link.active .pc-micon i {
    color: #fff !important;
}
.pc-navbar .pc-link:hover {
    background: #e3f2fd;
    color: #1976d2;
}
.pc-navbar {
    padding-top: 8px;
    padding-bottom: 8px;
    padding-left: 6px;
    padding-right: 6px;
}
</style>
