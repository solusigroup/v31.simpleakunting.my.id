<?php

class Penjualan extends Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Fungsi helper pribadi untuk memeriksa apakah periode transaksi terkunci.
     * @param string $tanggal Tanggal transaksi yang akan diperiksa.
     */
    private function _checkPeriodLock($tanggal) {
        $tutupBukuModel = $this->model('TutupBuku');
        if ($tutupBukuModel->isPeriodClosed($tanggal)) {
            Flash::setFlash('Gagal! Periode untuk tanggal transaksi ini sudah ditutup dan tidak bisa diubah atau dihapus.', 'danger');
            // Arahkan kembali ke halaman sebelumnya atau halaman utama modul
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASEURL . '/penjualan'));
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Jurnal Penjualan';
        $data['penjualan'] = $this->model('Penjualan')->getAllPenjualan();
        $this->view('templates/header', $data);
        $this->view('penjualan/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Penjualan Baru';
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan();
        $data['barang'] = $this->model('Persediaan')->getAllBarang();
        $data['akun_kas'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('penjualan/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        // PERLINDUNGAN: Periksa tanggal transaksi sebelum menyimpan
        $this->_checkPeriodLock($_POST['tanggal_faktur']);
        
        $pelanggan = $this->model('Pelanggan')->getPelangganById($_POST['id_pelanggan']);
        $_POST['nama_pelanggan'] = $pelanggan['nama_pelanggan'];

        if ($this->model('Penjualan')->simpanPenjualan($_POST)) {
            Flash::setFlash('Transaksi penjualan berhasil disimpan dan dijurnal.', 'success');
            header('Location: ' . BASEURL . '/penjualan');
        } else {
            // Pesan error Flash (misalnya, stok tidak cukup) sudah di-set di dalam Model
            header('Location: ' . BASEURL . '/penjualan/tambah');
        }
        exit;
    }

    public function lihat($id) {
        $data['judul'] = 'Detail Faktur Penjualan';
        $data['penjualan'] = $this->model('Penjualan')->getPenjualanByIdWithDetails($id);
        if (!$data['penjualan']) {
            Flash::setFlash('Faktur penjualan tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/penjualan');
            exit;
        }
        $this->view('templates/header', $data);
        $this->view('penjualan/lihat', $data);
        $this->view('templates/footer');
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/penjualan');
            exit;
        }
        
        // PERLINDUNGAN: Ambil data penjualan untuk mendapatkan tanggalnya, lalu periksa
        $penjualan = $this->model('Penjualan')->getPenjualanByIdWithDetails($id);
        if ($penjualan) {
            $this->_checkPeriodLock($penjualan['tanggal_faktur']);
        }

        if ($this->model('Penjualan')->hapusPenjualan($id)) {
            Flash::setFlash('Transaksi penjualan berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/penjualan');
        exit;
    }
}

