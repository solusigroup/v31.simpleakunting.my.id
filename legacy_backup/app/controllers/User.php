<?php

class User extends Controller {
    /**
     * Constructor ini berjalan secara otomatis setiap kali controller User dipanggil.
     * Fungsinya sebagai gerbang keamanan.
     */
    public function __construct()
    {
        // **PERBAIKAN: Jalankan constructor dari Controller induk terlebih dahulu**
        // Ini akan memastikan koneksi database ($this->db) dibuat.
        parent::__construct();

        // Setelah itu, baru jalankan logika proteksi role.
        if (!Auth::hasRole('Admin')) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk halaman ini.', 'danger');
            header('Location: ' . BASEURL);
            exit;
        }
    }

    /**
     * Menampilkan halaman utama (daftar) pengguna.
     */
    public function index() {
        $data['judul'] = 'Data Pengguna';
        $data['users'] = $this->model('User')->getAllUsers();
        $this->view('templates/header', $data);
        $this->view('user/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Menampilkan form untuk menambah pengguna baru.
     */
    public function tambah() {
        $data['judul'] = 'Tambah Pengguna Baru';
        $this->view('templates/header', $data);
        $this->view('user/tambah');
        $this->view('templates/footer');
    }

    /**
     * Memproses data dari form tambah pengguna.
     */
    public function simpan() {
        if ($this->model('User')->tambahDataUser($_POST) > 0) {
            Flash::setFlash('Pengguna baru berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan pengguna baru.', 'danger');
        }
        header('Location: ' . BASEURL . '/user');
        exit;
    }

    /**
     * Menampilkan form untuk mengedit data pengguna.
     */
    public function edit($id) {
        $data['judul'] = 'Edit Data Pengguna';
        $data['user'] = $this->model('User')->getUserById($id);
        $this->view('templates/header', $data);
        $this->view('user/edit', $data);
        $this->view('templates/footer');
    }

    /**
     * Memproses data dari form edit pengguna.
     */
    public function update() {
        if ($this->model('User')->ubahDataUser($_POST) > 0) {
            Flash::setFlash('Data pengguna berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data pengguna.', 'danger');
        }
        header('Location: ' . BASEURL . '/user');
        exit;
    }

    /**
     * Menghapus data pengguna.
     */
    public function hapus($id) {
        if ($this->model('User')->hapusDataUser($id) > 0) {
            Flash::setFlash('Data pengguna berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data pengguna.', 'danger');
        }
        header('Location: ' . BASEURL . '/user');
        exit;
    }
}

