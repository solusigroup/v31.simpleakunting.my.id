<?php

class Pembelian extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Fungsi helper pribadi untuk memeriksa apakah periode transaksi terkunci.
     */
    private function _checkPeriodLock($tanggal) {
        $tutupBukuModel = $this->model('TutupBuku');
        if ($tutupBukuModel->isPeriodClosed($tanggal)) {
            Flash::setFlash('Gagal! Periode untuk tanggal transaksi ini sudah ditutup dan tidak bisa diubah atau dihapus.', 'danger');
            // Arahkan kembali ke halaman sebelumnya atau halaman utama modul
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASEURL . '/pembelian'));
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Jurnal Pembelian';
        $data['pembelian'] = $this->model('Pembelian')->getAllPembelian();
        $this->view('templates/header', $data);
        $this->view('pembelian/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Pembelian Baru';
        $data['pemasok'] = $this->model('Pemasok')->getAllPemasok();
        $data['barang'] = $this->model('Persediaan')->getAllBarang();
        $data['akun_kas'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('pembelian/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        // PERLINDUNGAN: Periksa tanggal transaksi sebelum menyimpan
        $this->_checkPeriodLock($_POST['tanggal_faktur']);

        $pemasok = $this->model('Pemasok')->getPemasokById($_POST['id_pemasok']);
        $_POST['nama_pemasok'] = $pemasok['nama_pemasok'];
        if ($this->model('Pembelian')->simpanPembelian($_POST)) {
            Flash::setFlash('Transaksi pembelian berhasil disimpan dan dijurnal.', 'success');
            header('Location: ' . BASEURL . '/pembelian');
            exit;
        } else {
            header('Location: ' . BASEURL . '/pembelian/tambah');
            exit;
        }
    }
    
    public function lihat($id) {
        $data['judul'] = 'Detail Faktur Pembelian';
        $data['pembelian'] = $this->model('Pembelian')->getPembelianByIdWithDetails($id);

        if (!$data['pembelian']) {
            Flash::setFlash('Faktur pembelian tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/pembelian');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('pembelian/lihat', $data);
        $this->view('templates/footer');
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/pembelian');
            exit;
        }

        // PERLINDUNGAN: Ambil data pembelian untuk mendapatkan tanggalnya, lalu periksa
        $pembelian = $this->model('Pembelian')->getPembelianByIdWithDetails($id);
        if ($pembelian) {
            $this->_checkPeriodLock($pembelian['tanggal_faktur']);
        }
        
        if ($this->model('Pembelian')->hapusPembelian($id)) {
            Flash::setFlash('Transaksi pembelian berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/pembelian');
        exit;
    }
}

