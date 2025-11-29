<?php

class Pemasok extends Controller {
    public function index() {
        $data['judul'] = 'Data Pemasok';
        $data['pemasok'] = $this->model('Pemasok')->getAllPemasok();
        $this->view('templates/header', $data);
        $this->view('pemasok/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Data Pemasok';
        $this->view('templates/header', $data);
        $this->view('pemasok/tambah');
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Pemasok')->tambahDataPemasok($_POST) > 0) {
            Flash::setFlash('Data pemasok berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Data Pemasok';
        $data['pemasok'] = $this->model('Pemasok')->getPemasokById($id);
        $this->view('templates/header', $data);
        $this->view('pemasok/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Pemasok')->ubahDataPemasok($_POST) > 0) {
            Flash::setFlash('Data pemasok berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Pemasok')->hapusDataPemasok($id) > 0) {
            Flash::setFlash('Data pemasok berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data pemasok.', 'danger');
        }
        header('Location: ' . BASEURL . '/pemasok');
        exit;
    }
}
