<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\JenisPinjamanController;
use App\Http\Controllers\JenisSimpananController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KasController;

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // =====================================================
    // DASHBOARD - All authenticated users
    // =====================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =====================================================
    // MASTER DATA - Read access for manajer+, Write access for admin+
    // =====================================================
    
    // Read-only routes for manajer (index, show)
    Route::middleware('role:superuser,admin,manajer')->group(function () {
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');
        Route::get('pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
        Route::get('pemasok/{pemasok}', [PemasokController::class, 'show'])->name('pemasok.show');
        Route::get('persediaan', [PersediaanController::class, 'index'])->name('persediaan.index');
        Route::get('akun', [AkunController::class, 'index'])->name('akun.index');
    });
    
    // Write routes for admin+ (create, store, edit, update, destroy)
    Route::middleware('role:superuser,admin')->group(function () {
        Route::get('pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
        Route::post('pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::delete('pelanggan/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
        
        Route::get('pemasok/create', [PemasokController::class, 'create'])->name('pemasok.create');
        Route::post('pemasok', [PemasokController::class, 'store'])->name('pemasok.store');
        Route::get('pemasok/{pemasok}/edit', [PemasokController::class, 'edit'])->name('pemasok.edit');
        Route::put('pemasok/{pemasok}', [PemasokController::class, 'update'])->name('pemasok.update');
        Route::delete('pemasok/{pemasok}', [PemasokController::class, 'destroy'])->name('pemasok.destroy');
        
        Route::get('persediaan/create', [PersediaanController::class, 'create'])->name('persediaan.create');
        Route::post('persediaan', [PersediaanController::class, 'store'])->name('persediaan.store');
        Route::get('persediaan/{persediaan}/edit', [PersediaanController::class, 'edit'])->name('persediaan.edit');
        Route::put('persediaan/{persediaan}', [PersediaanController::class, 'update'])->name('persediaan.update');
        Route::delete('persediaan/{persediaan}', [PersediaanController::class, 'destroy'])->name('persediaan.destroy');
        
        Route::get('akun/create', [AkunController::class, 'create'])->name('akun.create');
        Route::post('akun', [AkunController::class, 'store'])->name('akun.store');
        Route::get('akun/{akun}/edit', [AkunController::class, 'edit'])->name('akun.edit');
        Route::put('akun/{akun}', [AkunController::class, 'update'])->name('akun.update');
        Route::delete('akun/{akun}', [AkunController::class, 'destroy'])->name('akun.destroy');
        
        // Jenis Pinjaman CRUD
        Route::resource('jenis-pinjaman', JenisPinjamanController::class);
        
        // Jenis Simpanan CRUD
        Route::resource('jenis-simpanan', JenisSimpananController::class);
    });

    // =====================================================
    // TRANSAKSI - All authenticated users
    // =====================================================
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('pembelian', PembelianController::class);
    Route::resource('jurnal', JurnalController::class);
    Route::resource('penerimaan', PenerimaanController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::get('kas', [KasController::class, 'index'])->name('kas.index');
    Route::get('kas/transfer', [KasController::class, 'transfer'])->name('kas.transfer');
    Route::post('kas/transfer', [KasController::class, 'storeTransfer'])->name('kas.storeTransfer');

    // =====================================================
    // LAPORAN - Semua role (termasuk staff)
    // =====================================================
    Route::middleware('role:superuser,admin,manajer,staff')->group(function () {
        Route::get('bukubesar', [BukuBesarController::class, 'index'])->name('bukubesar.index');
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/neraca', [LaporanController::class, 'neraca'])->name('laporan.neraca');
        Route::get('/laporan/neraca/pdf', [LaporanController::class, 'neracaPdf'])->name('laporan.neraca.pdf');
        Route::get('/laporan/labarugi', [LaporanController::class, 'labaRugi'])->name('laporan.labarugi');
        Route::get('/laporan/labarugi/pdf', [LaporanController::class, 'labaRugiPdf'])->name('laporan.labarugi.pdf');
        Route::get('/laporan/aruskas-langsung', [LaporanController::class, 'arusKasLangsung'])->name('laporan.aruskas_langsung');
        Route::get('/laporan/aruskas-tidak-langsung', [LaporanController::class, 'arusKasTidakLangsung'])->name('laporan.aruskas_tidak_langsung');
        Route::get('/laporan/perubahan-ekuitas', [LaporanController::class, 'perubahanEkuitas'])->name('laporan.perubahan_ekuitas');
        Route::get('/laporan/persediaan', [LaporanController::class, 'persediaan'])->name('laporan.persediaan');
    });

    // =====================================================
    // PENGATURAN - Admin, Superuser
    // =====================================================
    Route::middleware('role:superuser,admin')->group(function () {
        Route::get('perusahaan', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
        Route::put('perusahaan', [PerusahaanController::class, 'update'])->name('perusahaan.update');
        Route::resource('users', UserController::class);
    });

    // =====================================================
    // KOPERASI SIMPAN PINJAM - All authenticated users
    // =====================================================
    
    // Anggota
    Route::resource('anggota', \App\Http\Controllers\AnggotaController::class);
    Route::get('anggota/{id}/kartu', [\App\Http\Controllers\AnggotaController::class, 'kartu'])->name('anggota.kartu');

    // Simpanan
    Route::resource('simpanan', \App\Http\Controllers\SimpananController::class);
    Route::get('simpanan-setor', [\App\Http\Controllers\SimpananController::class, 'setor'])->name('simpanan.setor');
    Route::get('simpanan-tarik', [\App\Http\Controllers\SimpananController::class, 'tarik'])->name('simpanan.tarik');
    Route::get('simpanan-kartu/{id_anggota}', [\App\Http\Controllers\SimpananController::class, 'kartu'])->name('simpanan.kartu');

    // Pinjaman - simulasi harus SEBELUM resource agar tidak tertangkap oleh {pinjaman}
    Route::post('pinjaman/simulasi', [\App\Http\Controllers\PinjamanController::class, 'simulasi'])->name('pinjaman.simulasi');
    Route::resource('pinjaman', \App\Http\Controllers\PinjamanController::class);
    Route::post('pinjaman/{id}/submit', [\App\Http\Controllers\PinjamanController::class, 'submit'])->name('pinjaman.submit');
    Route::get('pinjaman/{id}/pencairan', [\App\Http\Controllers\PinjamanController::class, 'pencairanForm'])->name('pinjaman.pencairan');
    Route::post('pinjaman/{id}/cairkan', [\App\Http\Controllers\PinjamanController::class, 'cairkan'])->name('pinjaman.cairkan');
    Route::get('pinjaman/{id}/angsuran', [\App\Http\Controllers\PinjamanController::class, 'angsuranForm'])->name('pinjaman.angsuran');
    Route::post('pinjaman/{id}/bayar', [\App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman.bayar');
    Route::get('pinjaman/{id}/pelunasan', [\App\Http\Controllers\PinjamanController::class, 'pelunasanForm'])->name('pinjaman.pelunasan');
    Route::post('pinjaman/{id}/lunasi', [\App\Http\Controllers\PinjamanController::class, 'lunasi'])->name('pinjaman.lunasi');

    // Approval Workflow - Manajer, Admin, Superuser
    Route::middleware('role:superuser,admin,manajer')->group(function () {
        Route::get('approval', [\App\Http\Controllers\ApprovalController::class, 'inbox'])->name('approval.inbox');
        Route::post('approval/{module}/{id}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('approval/{module}/{id}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approval.reject');
    });

    // Laporan Koperasi - Manajer, Admin, Superuser
    Route::middleware('role:superuser,admin,manajer')->group(function () {
        Route::get('laporan/simpanan', [LaporanController::class, 'laporanSimpanan'])->name('laporan.simpanan');
        Route::get('laporan/pinjaman-aktif', [LaporanController::class, 'laporanPinjamanAktif'])->name('laporan.pinjaman_aktif');
        Route::get('laporan/kolektibilitas', [LaporanController::class, 'laporanKolektibilitas'])->name('laporan.kolektibilitas');
        Route::get('laporan/aging', [LaporanController::class, 'laporanAgingPinjaman'])->name('laporan.aging');
    });

    // =====================================================
    // DATABASE MANAGEMENT - Superuser Only
    // =====================================================
    Route::middleware('role:superuser')->group(function () {
        Route::get('database', [\App\Http\Controllers\DatabaseController::class, 'index'])->name('database.index');
        Route::post('database/truncate', [\App\Http\Controllers\DatabaseController::class, 'truncate'])->name('database.truncate');
        Route::post('database/fresh', [\App\Http\Controllers\DatabaseController::class, 'fresh'])->name('database.fresh');
        Route::post('database/drop', [\App\Http\Controllers\DatabaseController::class, 'drop'])->name('database.drop');
        Route::post('database/seed', [\App\Http\Controllers\DatabaseController::class, 'seed'])->name('database.seed');
    });

    // =====================================================
    // IMPORT & EXPORT DATA - Manajer, Admin, Superuser
    // =====================================================
    Route::middleware('role:superuser,admin,manajer')->group(function () {
        Route::get('import-export', [\App\Http\Controllers\ImportExportController::class, 'index'])->name('import-export.index');
        Route::get('import-export/export/{module}', [\App\Http\Controllers\ImportExportController::class, 'export'])->name('import-export.export');
        Route::get('import-export/template/{module}', [\App\Http\Controllers\ImportExportController::class, 'template'])->name('import-export.template');
        Route::post('import-export/import/{module}', [\App\Http\Controllers\ImportExportController::class, 'import'])->name('import-export.import');
        Route::get('import-export/export-all', [\App\Http\Controllers\ImportExportController::class, 'exportAll'])->name('import-export.export-all');
    });
});

