<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Simple Akunting')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: .875rem;
            overflow-x: hidden; /* Prevent horizontal scroll when sidebar toggles */
        }
        .feather {
            width: 16px;
            height: 16px;
            vertical-align: text-bottom;
        }
        
        /* Sidebar Styles */
        #sidebarMenu {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            padding-top: 48px; /* Height of navbar */
            background-color: #f8f9fa;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            transition: margin-left 0.3s ease-in-out;
            width: 250px;
        }

        /* Sidebar Hidden State (Desktop & Mobile) */
        #sidebarMenu.collapsed {
            margin-left: -250px;
        }

        /* Main Content Styles */
        main {
            transition: margin-left 0.3s ease-in-out;
            padding-top: 48px; /* Match navbar height */
        }

        /* When sidebar is visible (default on desktop, toggled on mobile) */
        @media (min-width: 768px) {
            main {
                margin-left: 250px;
            }
            main.expanded {
                margin-left: 0;
            }
        }

        .nav-link {
            font-weight: 500;
            color: #333;
        }
        .nav-link .feather {
            margin-right: 4px;
            color: #727272;
        }
        .nav-link.active {
            color: #2470dc;
        }
        .nav-link:hover .feather,
        .nav-link.active .feather {
            color: inherit;
        }

        /* Dropdown/Accordion Styles */
        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            cursor: pointer;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-heading:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .sidebar-heading .feather-chevron-down {
            transition: transform 0.2s;
        }
        .sidebar-heading[aria-expanded="true"] .feather-chevron-down {
            transform: rotate(180deg);
        }
        .nav-group {
            padding-left: 10px;
        }

        /* Navbar */
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
            width: 250px; /* Match sidebar width */
            transition: width 0.3s ease-in-out;
        }
        /* When sidebar is collapsed, brand width adjusts or stays */
        
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
    </style>
</head>
<body>
    
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Simple Akunting</a>
        <button class="btn btn-link text-white order-0" id="sidebarToggle">
            <span data-feather="menu"></span>
        </button>
        <div class="w-100"></div>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link px-3 border-0 text-white" style="background-color: rgba(0, 0, 0, .25); box-shadow: inset 1px 0 0 rgba(0, 0, 0, .25); height: 100%; font-size: 1rem; padding-top: .75rem; padding-bottom: .75rem;">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="">
                <div class="position-sticky pt-3" id="sidebarAccordion">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}">
                                <span data-feather="home"></span>
                                Dashboard
                            </a>
                        </li>
                    </ul>

                    <!-- Master Data -->
                    @php
                        $isMasterActive = request()->routeIs('pelanggan.*') || request()->routeIs('pemasok.*') || request()->routeIs('persediaan.*') || request()->routeIs('akun.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#masterDataMenu" 
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                        <span>Master Data</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="masterDataMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                                    <span data-feather="users"></span>
                                    Pelanggan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pemasok.*') ? 'active' : '' }}" href="{{ route('pemasok.index') }}">
                                    <span data-feather="truck"></span>
                                    Pemasok
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('persediaan.*') ? 'active' : '' }}" href="{{ route('persediaan.index') }}">
                                    <span data-feather="box"></span>
                                    Persediaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('akun.*') ? 'active' : '' }}" href="{{ route('akun.index') }}">
                                    <span data-feather="list"></span>
                                    Akun (COA)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Transaksi -->
                    @php
                        $isTransaksiActive = request()->routeIs('penjualan.*') || request()->routeIs('pembelian.*') || request()->routeIs('jurnal.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#transaksiMenu" 
                        aria-expanded="{{ $isTransaksiActive ? 'true' : 'false' }}">
                        <span>Transaksi</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isTransaksiActive ? 'show' : '' }}" id="transaksiMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
                                    <span data-feather="shopping-cart"></span>
                                    Penjualan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                                    <span data-feather="shopping-bag"></span>
                                    Pembelian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}" href="{{ route('jurnal.index') }}">
                                    <span data-feather="file-text"></span>
                                    Jurnal Umum
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kas & Bank -->
                    @php
                        $isKasActive = request()->routeIs('penerimaan.*') || request()->routeIs('pembayaran.*') || request()->routeIs('kas.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#kasMenu" 
                        aria-expanded="{{ $isKasActive ? 'true' : 'false' }}">
                        <span>Kas & Bank</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isKasActive ? 'show' : '' }}" id="kasMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('penerimaan.*') ? 'active' : '' }}" href="{{ route('penerimaan.index') }}">
                                    <span data-feather="arrow-down-circle"></span>
                                    Penerimaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}" href="{{ route('pembayaran.index') }}">
                                    <span data-feather="arrow-up-circle"></span>
                                    Pembayaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('kas.*') ? 'active' : '' }}" href="{{ route('kas.index') }}">
                                    <span data-feather="dollar-sign"></span>
                                    Transaksi Kas
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Laporan -->
                    @php
                        $isLaporanActive = request()->routeIs('bukubesar.*') || request()->routeIs('laporan.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#laporanMenu" 
                        aria-expanded="{{ $isLaporanActive ? 'true' : 'false' }}">
                        <span>Laporan</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isLaporanActive ? 'show' : '' }}" id="laporanMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('bukubesar.*') ? 'active' : '' }}" href="{{ route('bukubesar.index') }}">
                                    <span data-feather="book"></span>
                                    Buku Besar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.neraca') ? 'active' : '' }}" href="{{ route('laporan.neraca') }}">
                                    <span data-feather="bar-chart-2"></span>
                                    Neraca
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.labarugi') ? 'active' : '' }}" href="{{ route('laporan.labarugi') }}">
                                    <span data-feather="trending-up"></span>
                                    Laba Rugi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.aruskas_langsung') ? 'active' : '' }}" href="{{ route('laporan.aruskas_langsung') }}">
                                    <span data-feather="activity"></span>
                                    Arus Kas (Langsung)
                                </a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.aruskas_tidak_langsung') ? 'active' : '' }}" href="{{ route('laporan.aruskas_tidak_langsung') }}">
                                <span data-feather="activity"></span>
                                Arus Kas (Tidak Langsung)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.perubahan_ekuitas') ? 'active' : '' }}" href="{{ route('laporan.perubahan_ekuitas') }}">
                                <span data-feather="pie-chart"></span>
                                Perubahan Ekuitas
                            </a>
                        </li>
                        <li class="nav-item">
                    </ul>
                    </div>

                    <!-- Koperasi Simpan Pinjam -->
                    @php
                        $isKoperasiActive = request()->routeIs('anggota.*') || request()->routeIs('simpanan.*') || request()->routeIs('pinjaman.*') || request()->routeIs('approval.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#koperasiMenu" 
                        aria-expanded="{{ $isKoperasiActive ? 'true' : 'false' }}">
                        <span>🏦 Simpan Pinjam</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isKoperasiActive ? 'show' : '' }}" id="koperasiMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}" href="{{ route('anggota.index') }}">
                                    <span data-feather="users"></span>
                                    Anggota
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('simpanan.*') ? 'active' : '' }}" href="{{ route('simpanan.index') }}">
                                    <span data-feather="save"></span>
                                    Simpanan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pinjaman.*') ? 'active' : '' }}" href="{{ route('pinjaman.index') }}">
                                    <span data-feather="credit-card"></span>
                                    Pinjaman
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('approval.*') ? 'active' : '' }}" href="{{ route('approval.inbox') }}">
                                    <span data-feather="check-square"></span>
                                    Approval
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Admin -->
                    @php
                        $isAdminActive = request()->routeIs('perusahaan.*') || request()->routeIs('users.*');
                    @endphp
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#adminMenu" 
                        aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}">
                        <span>Admin</span>
                        <span data-feather="chevron-down" class="feather-chevron-down"></span>
                    </h6>
                    <div class="collapse {{ $isAdminActive ? 'show' : '' }}" id="adminMenu" data-bs-parent="#sidebarAccordion">
                        <ul class="nav flex-column mb-2 nav-group">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('perusahaan.*') ? 'active' : '' }}" href="{{ route('perusahaan.edit') }}">
                                    <span data-feather="settings"></span>
                                    Profil Perusahaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <span data-feather="users"></span>
                                    Manajemen User
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.index') }}">
                                    <span data-feather="upload-cloud"></span>
                                    Import/Export Data
                                </a>
                            </li>
                            @if(auth()->user() && auth()->user()->role === 'superuser')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('database.*') ? 'active' : '' }}" href="{{ route('database.index') }}">
                                    <span data-feather="database"></span>
                                    Manajemen Database
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @if(session('success'))
                    <div class="alert alert-success mt-3 alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mt-3 alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
    <script>
        (function () {
            'use strict'
            feather.replace({ 'aria-hidden': 'true' })

            // Sidebar Toggle Logic
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebarMenu');
            const main = document.querySelector('main');

            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded');
            });
        })()
    </script>
    @stack('scripts')
</body>
</html>
