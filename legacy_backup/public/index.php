<?php
// Aktifkan pelaporan error selama masa pengembangan
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. **MEMUAT AUTOLOADER COMPOSER (INI YANG PALING PENTING)**
// Path ini akan memuat semua library eksternal, termasuk PhpSpreadsheet.
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Memuat file inisialisasi aplikasi kita (setelah library siap)
require_once '../app/init.php';

// 3. Menjalankan aplikasi
$app = new App();

