<?php

class Aset extends Controller {
    public function index() {
        $data['judul'] = 'Master Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAllAset();
        $this->view('templates/header', $data);
        $this->view('aset/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Aset Tetap';
        $data['akun'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('aset/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Aset')->tambahDataAset($_POST) > 0) {
            Flash::setFlash('Aset tetap berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan aset tetap.', 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Aset Tetap';
        $data['aset'] = $this->model('Aset')->getAsetById($id);
        $data['akun'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('aset/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Aset')->ubahDataAset($_POST) > 0) {
            Flash::setFlash('Data aset tetap berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Tidak ada perubahan data yang disimpan.', 'info');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Aset')->hapusDataAset($id) > 0) {
            Flash::setFlash('Aset tetap berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus aset tetap.', 'danger');
        }
        header('Location: ' . BASEURL . '/aset');
        exit;
    }
}

