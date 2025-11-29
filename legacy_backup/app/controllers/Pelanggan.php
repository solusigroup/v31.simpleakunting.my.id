<?php

class Pelanggan extends Controller {
    public function index() {
        $data['judul'] = 'Data Pelanggan';
        $data['pelanggan'] = $this->model('Pelanggan')->getAllPelanggan();
        $this->view('templates/header', $data);
        $this->view('pelanggan/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Data Pelanggan';
        $this->view('templates/header', $data);
        $this->view('pelanggan/tambah');
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Pelanggan')->tambahDataPelanggan($_POST) > 0) {
            Flash::setFlash('Data pelanggan berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan data pelanggan.', 'danger');
        }
        header('Location: ' . BASEURL . '/pelanggan');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Data Pelanggan';
        $data['pelanggan'] = $this->model('Pelanggan')->getPelangganById($id);
        $this->view('templates/header', $data);
        $this->view('pelanggan/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Pelanggan')->ubahDataPelanggan($_POST) > 0) {
            Flash::setFlash('Data pelanggan berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data pelanggan.', 'danger');
        }
        header('Location: ' . BASEURL . '/pelanggan');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Pelanggan')->hapusDataPelanggan($id) > 0) {
            Flash::setFlash('Data pelanggan berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data pelanggan.', 'danger');
        }
        header('Location: ' . BASEURL . '/pelanggan');
        exit;
    }
}

