<?php

class Kas extends Controller {
    public function __construct() {
        parent::__construct();
    }

    private function _checkPeriodLock($tanggal) {
        $tutupBukuModel = $this->model('TutupBuku');
        if ($tutupBukuModel->isPeriodClosed($tanggal)) {
            Flash::setFlash('Gagal! Periode untuk tanggal transaksi ini sudah ditutup dan tidak bisa diubah atau dihapus.', 'danger');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASEURL . '/kas'));
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Transaksi Kas & Bank';
        $data['transaksi'] = $this->model('Kas')->getAllTransaksi();
        $this->view('templates/header', $data);
        $this->view('kas/index', $data);
        $this->view('templates/footer');
    }

    /**
     * FUNGSI YANG DIPERBARUI: Sekarang lebih "tahan banting" terhadap nilai NULL.
     */
    public function tambah() {
        $data['judul'] = 'Tambah Transaksi Kas & Bank';
        
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        $grouped_accounts = [];

        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] == 'Detail') {
                // PERBAIKAN: Gunakan '??' untuk memberikan nilai default string kosong jika data NULL.
                if (trim(strtolower($akun['sub_grup_akun'] ?? '')) == 'kas & bank') {
                    $data['akun_kas_list'][] = $akun;
                } else {
                    $grup = $akun['grup_akun'] ?? 'Lain-lain';
                    $grouped_accounts[$grup][] = $akun;
                }
            }
        }
        $data['akun_lawan_list'] = $grouped_accounts;

        $this->view('templates/header', $data);
        $this->view('kas/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        $this->_checkPeriodLock($_POST['tanggal']);
        if ($this->model('Kas')->simpanTransaksi($_POST)) {
            Flash::setFlash('Transaksi kas berhasil disimpan.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }
    
    /**
     * FUNGSI YANG DIPERBARUI: Menggunakan logika filter yang sama dengan tambah().
     */
    public function edit($id) {
        $data['judul'] = 'Edit Transaksi Kas & Bank';
        $data['transaksi'] = $this->model('Kas')->getTransaksiById($id);
        
        $all_accounts = $this->model('Akun')->getAllAkun();
        $data['akun_kas_list'] = [];
        $grouped_accounts = [];
        foreach ($all_accounts as $akun) {
            if ($akun['tipe_akun'] == 'Detail') {
                // PERBAIKAN: Gunakan '??' untuk memberikan nilai default string kosong jika data NULL.
                if (trim(strtolower($akun['sub_grup_akun'] ?? '')) == 'kas & bank') {
                    $data['akun_kas_list'][] = $akun;
                } else {
                    $grup = $akun['grup_akun'] ?? 'Lain-lain';
                    $grouped_accounts[$grup][] = $akun;
                }
            }
        }
        $data['akun_lawan_list'] = $grouped_accounts;

        $this->view('templates/header', $data);
        $this->view('kas/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        $this->_checkPeriodLock($_POST['tanggal']);
        if ($this->model('Kas')->updateTransaksi($_POST)) {
            Flash::setFlash('Transaksi kas berhasil diperbarui.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }

    public function hapus($id) {
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk tindakan ini.', 'danger');
            header('Location: ' . BASEURL . '/kas');
            exit;
        }
        $transaksi = $this->model('Kas')->getTransaksiById($id);
        if ($transaksi) {
            $this->_checkPeriodLock($transaksi['tanggal']);
        }
        if ($this->model('Kas')->hapusTransaksi($id)) {
            Flash::setFlash('Transaksi kas berhasil dibatalkan.', 'success');
        }
        header('Location: ' . BASEURL . '/kas');
        exit;
    }
}

