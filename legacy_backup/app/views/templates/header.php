<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['judul']; ?> - #SIMPLE_AKUNTING</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #212529;
            --sidebar-link-color: #adb5bd;
            --sidebar-link-hover: #fff;
            --sidebar-link-active: #fff;
        }
        body {
            background-color: #f8f9fa;
            transition: padding-left 0.3s ease;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            padding: 1rem;
            transition: transform 0.3s ease;
            z-index: 1030;
            overflow-y: auto;
        }
        .sidebar-brand {
            color: #fff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .sidebar .nav-link {
            color: var(--sidebar-link-color);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            color: var(--sidebar-link-hover);
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: var(--sidebar-link-active);
            background-color: #0d6efd;
            font-weight: bold;
        }
        .sidebar .nav-link .bi {
            margin-right: 0.75rem;
        }
        .sidebar .nav-link.collapsed .bi-chevron-down {
            transition: transform 0.2s ease-in-out;
        }
        .sidebar .nav-link:not(.collapsed) .bi-chevron-down {
            transform: rotate(90deg);
        }
        .main-wrapper {
            transition: margin-left 0.3s ease;
            margin-left: var(--sidebar-width);
        }
        .topbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        body.sidebar-collapsed .sidebar {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }
        body.sidebar-collapsed .main-wrapper {
            margin-left: 0;
        }
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(calc(-1 * var(--sidebar-width)));
            }
            .main-wrapper {
                margin-left: 0;
            }
            body.sidebar-collapsed .sidebar {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

<?php
    $url_parts = explode('/', $_GET['url'] ?? 'home');
    $current_controller = strtolower($url_parts[0]);
    
    $master_controllers = ['akun', 'pelanggan', 'pemasok', 'persediaan', 'aset'];
    $transaksi_controllers = ['penjualan', 'pembelian', 'penerimaan', 'pembayaran', 'kas', 'penyesuaian', 'jurnal', 'tutupbuku'];
    $laporan_controllers = ['laporan', 'analisis'];
?>

<!-- Sidebar Vertikal -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-journal-richtext"></i>#Simple_Akunting
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_controller == 'dashboard' || $current_controller == 'home') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>/dashboard">
                <i class="bi bi-house-door-fill"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (in_array($current_controller, $master_controllers)) ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#masterDataCollapse" role="button">
                <i class="bi bi-stack"></i> Master Data <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo (in_array($current_controller, $master_controllers)) ? 'show' : ''; ?>" id="masterDataCollapse">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/akun">Daftar Akun</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/pelanggan">Pelanggan</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/pemasok">Pemasok</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/persediaan">Persediaan</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/aset">Aset Tetap</a>
            </div>
        </li>
         <li class="nav-item">
            <a class="nav-link <?php echo (in_array($current_controller, $transaksi_controllers)) ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#transaksiCollapse" role="button">
                <i class="bi bi-arrow-down-up"></i> Transaksi <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo (in_array($current_controller, $transaksi_controllers)) ? 'show' : ''; ?>" id="transaksiCollapse">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/penjualan">Penjualan</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/pembelian">Pembelian</a>
                <hr class="border-secondary">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/penerimaan">Penerimaan</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/pembayaran">Pembayaran</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/kas">Kas & Bank</a>
                <hr class="border-secondary">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/penyesuaian">Penyesuaian</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/jurnal">Jurnal Manual</a>
                <?php if (Auth::isAdmin() || Auth::isManager()): ?>
                <hr class="border-secondary">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/tutupBuku">Tutup Buku Periode</a>
                <?php endif; ?>
            </div>
        </li>
        <?php if (Auth::isAdmin() || Auth::isManager()): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (in_array($current_controller, $laporan_controllers)) ? '' : 'collapsed'; ?>" data-bs-toggle="collapse" href="#laporanCollapse" role="button">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan & Analisis <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo (in_array($current_controller, $laporan_controllers)) ? 'show' : ''; ?>" id="laporanCollapse">
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/bukuBesar">Buku Besar</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/neracaSaldo">Neraca Saldo</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/labaRugi">Laba Rugi</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/posisiKeuangan">Posisi Keuangan</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/perubahanEkuitas">Perubahan Ekuitas</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/arusKas">Arus Kas</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/laporan/neracaLajur">Neraca Lajur</a>
                <a class="nav-link ms-4" href="<?php echo BASEURL; ?>/analisis">Analisis Keuangan</a>
            </div>
        </li>
        <?php endif; ?>

        <li class="nav-item mt-auto pt-2 border-top border-secondary">
            <a class="nav-link" href="<?php echo BASEURL; ?>/panduan_pengguna.html" target="_blank">
                <i class="bi bi-question-circle-fill"></i> Bantuan
            </a>
        </li>
        <li class="nav-item mt-auto pt-2 border-top border-secondary">
            <a class="nav-link" href="<?php echo BASEURL; ?>/InfografisSimpleAkunting.html" target="_blank">
                <i class="bi bi-feather"></i> Info Grafis
            </a>
        </li>
        <li class="nav-item mt-auto pt-2 border-top border-secondary">
            <a class="nav-link" href="<?php echo BASEURL; ?>/umpan_balik.html" target="_blank">
                <i class="bi bi-feather2"></i> Feedback & Pemesanan
            </a>
        </li>
        <li class="nav-item mt-auto pt-2 border-top border-secondary">
            <a class="nav-link" href=https://analisis-keuangan-bum-de-4tfjedc.gamma.site/#detail" target="_blank"> 
                <i class="bi bi-feather2"></i> Analisa Laporan Keuangan Juara 2025
            </a>
        </li>
    </ul>
</aside>

<!-- Wrapper untuk Konten Utama dan Topbar -->
<div class="main-wrapper">
    <!-- Topbar (baris atas) -->
    <nav class="navbar navbar-expand-lg navbar-light topbar sticky-top">
        <div class="container-fluid">
            <!-- Tombol untuk Buka/Tutup Sidebar -->
            <button class="btn btn-outline-secondary" id="sidebar-toggle">
                <i class="bi bi-list"></i>
            </button>
            
            <!-- Menu Pengguna di Kanan -->
            <ul class="navbar-nav ms-auto">
                <?php if (Auth::isLoggedIn()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo Auth::user()['name']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (Auth::isAdmin()): ?>
                        <li><a class="dropdown-item" href="<?php echo BASEURL; ?>/perusahaan">Pengaturan Perusahaan</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASEURL; ?>/user">Manajemen User</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item disabled" href="#">(<?php echo Auth::user()['role']; ?>)</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASEURL; ?>/login/logout">Keluar</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Konten Utama Halaman -->
    <main class="p-4">
        <?php Flash::flash(); ?>

