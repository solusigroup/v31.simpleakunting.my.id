<?php

class Pembayaran extends Controller {

    public function __construct() {
        parent::__construct();
    }

    private function _checkPeriodLock($tanggal) {
        $tutupBukuModel = $this->model('TutupBuku');
        if ($tutupBukuModel->isPeriodClosed($tanggal)) {
            Flash::setFlash('Gagal! Periode untuk tanggal transaksi ini sudah ditutup dan tidak bisa diubah atau dihapus.', 'danger');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASEURL . '/pembayaran'));
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Pembayaran Pemasok';
        $data['pembayaran'] = $this->model('Pembayaran')->getAllPembayaran();
        $this->view('templates/header', $data);
        $this->view('pembayaran/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Pembayaran Pemasok';
        $data['pemasok'] = $this->model('Pemasok')->getAllPemasok();
        
        // Ambil semua akun
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        
        // Lakukan pemfilteran di sini, di dalam Controller
        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] == 'Detail') {
                // PERBAIKAN: Gunakan awalan '1.1' untuk mengidentifikasi Kas & Bank
                if (substr($akun['kode_akun'], 0, 3) == '1.1') {
                    $data['akun_kas_list'][] = $akun;
                }
            }
        }

        $this->view('templates/header', $data);
        $this->view('pembayaran/tambah', $data);
        $this->view('templates/footer');
    }

    public function getFaktur($id_pemasok) {
        header('Content-Type: application/json');
        $faktur = $this->model('Pembayaran')->getFakturBelumLunasByPemasok($id_pemasok);
        echo json_encode($faktur);
    }

    public function simpan() {
        $this->_checkPeriodLock($_POST['tanggal']);
        $pemasok = $this->model('Pemasok')->getPemasokById($_POST['id_pemasok']);
        $_POST['nama_pemasok'] = $pemasok['nama_pemasok'];

        if ($this->model('Pembayaran')->simpanPembayaran($_POST)) {
            Flash::setFlash('Pembayaran kepada pemasok berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/pembayaran');
            exit;
        } else {
            header('Location: ' . BASEURL . '/pembayaran/tambah');
            exit;
        }
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/pembayaran');
            exit;
        }
        
        Flash::setFlash('Fitur hapus pembayaran belum diimplementasikan sepenuhnya.', 'warning');
        header('Location: ' . BASEURL . '/pembayaran');
        exit;
    }
}