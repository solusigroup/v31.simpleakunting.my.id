<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('pelanggan', PelangganController::class);
    Route::resource('pemasok', PemasokController::class);
    Route::resource('persediaan', PersediaanController::class);
    
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('pembelian', PembelianController::class);

    Route::resource('akun', AkunController::class);
    Route::resource('jurnal', JurnalController::class);
    Route::get('bukubesar', [BukuBesarController::class, 'index'])->name('bukubesar.index');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/neraca', [LaporanController::class, 'neraca'])->name('laporan.neraca');
    Route::get('/laporan/labarugi', [LaporanController::class, 'labaRugi'])->name('laporan.labarugi');
    Route::get('/laporan/aruskas-langsung', [LaporanController::class, 'arusKasLangsung'])->name('laporan.aruskas_langsung');
    Route::get('/laporan/aruskas-tidak-langsung', [LaporanController::class, 'arusKasTidakLangsung'])->name('laporan.aruskas_tidak_langsung');
    Route::get('/laporan/perubahan-ekuitas', [LaporanController::class, 'perubahanEkuitas'])->name('laporan.perubahan_ekuitas');
    Route::get('/laporan/persediaan', [LaporanController::class, 'persediaan'])->name('laporan.persediaan');

    Route::get('perusahaan', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
    Route::put('perusahaan', [PerusahaanController::class, 'update'])->name('perusahaan.update');
    Route::resource('users', UserController::class);

    Route::resource('penerimaan', PenerimaanController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::get('kas', [KasController::class, 'index'])->name('kas.index');
    Route::get('kas/transfer', [KasController::class, 'transfer'])->name('kas.transfer');
    Route::post('kas/transfer', [KasController::class, 'storeTransfer'])->name('kas.storeTransfer');
});
