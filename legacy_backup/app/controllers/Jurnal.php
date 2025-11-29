<?php

class Jurnal extends Controller {

    public function index()
    {
        $data['judul'] = 'Jurnal Umum';
        $data['jurnal'] = $this->model('Jurnal')->getAllJurnal();
        
        $this->view('templates/header', $data);
        $this->view('jurnal/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        $data['judul'] = 'Tambah Entri Jurnal';
        $data['akun'] = $this->model('Akun')->getAllAkun();

        $this->view('templates/header', $data);
        $this->view('jurnal/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan()
    {
        $totalDebit = isset($_POST['details']['debit']) ? array_sum($_POST['details']['debit']) : 0;
        $totalKredit = isset($_POST['details']['kredit']) ? array_sum($_POST['details']['kredit']) : 0;

        if ($totalDebit !== $totalKredit || $totalDebit == 0) {
            Flash::setFlash('Gagal! Total Debit dan Kredit harus sama dan tidak boleh nol.', 'danger');
            header('Location: ' . BASEURL . '/jurnal/tambah');
            exit;
        }

        $formattedData = [
            'no_transaksi' => $_POST['no_transaksi'],
            'tanggal' => $_POST['tanggal'],
            'deskripsi' => $_POST['deskripsi'],
            'details' => []
        ];

        if (isset($_POST['details']['kode_akun'])) {
            foreach ($_POST['details']['kode_akun'] as $index => $kode_akun) {
                $formattedData['details'][] = [
                    'kode_akun' => $kode_akun,
                    'debit' => $_POST['details']['debit'][$index] ?? 0,
                    'kredit' => $_POST['details']['kredit'][$index] ?? 0
                ];
            }
        }

        if ($this->model('Jurnal')->simpanJurnal($formattedData) > 0) {
            Flash::setFlash('Entri jurnal berhasil disimpan.', 'success');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        } else {
            Flash::setFlash('Gagal menyimpan entri jurnal.', 'danger');
            header('Location: ' . BASEURL . '/jurnal/tambah');
            exit;
        }
    }

    /**
     * Menampilkan form untuk mengedit jurnal dengan logika yang lebih cerdas.
     */
    public function edit($id)
    {
        $data['judul'] = 'Edit Entri Jurnal';
        $jurnal_model = $this->model('Jurnal');
        $data['jurnal'] = $jurnal_model->getJurnalWithDetailsById($id);
        $data['akun'] = $this->model('Akun')->getAllAkun();

        // PERBAIKAN: Cek apakah jurnal ditemukan SEBELUM melanjutkan
        if ($data['jurnal'] === null) {
            Flash::setFlash('Gagal! Entri jurnal dengan ID tersebut tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }

        // Cek jika jurnal terkunci (logika ini sekarang aman)
        if ($data['jurnal']['is_locked']) {
            Flash::setFlash('Gagal! Jurnal ini terkunci dan tidak dapat diubah.', 'warning');
            header('Location: ' . BASEURL . '/jurnal');
            exit;
        }

        $this->view('templates/header', $data);
        $this->view('jurnal/edit', $data);
        $this->view('templates/footer');
    }

    public function update()
    {
        $totalDebit = isset($_POST['details']['debit']) ? array_sum($_POST['details']['debit']) : 0;
        $totalKredit = isset($_POST['details']['kredit']) ? array_sum($_POST['details']['kredit']) : 0;

        if ($totalDebit !== $totalKredit || $totalDebit == 0) {
            Flash::setFlash('Gagal! Total Debit dan Kredit harus sama dan tidak boleh nol.', 'danger');
            header('Location: ' . BASEURL . '/jurnal/edit/' . $_POST['id_jurnal']);
            exit;
        }
        
        $formattedData = [
            'id_jurnal' => $_POST['id_jurnal'],
            'no_transaksi' => $_POST['no_transaksi'],
            'tanggal' => $_POST['tanggal'],
            'deskripsi' => $_POST['deskripsi'],
            'details' => []
        ];
        
        if (isset($_POST['details']['kode_akun'])) {
            foreach ($_POST['details']['kode_akun'] as $index => $kode_akun) {
                $formattedData['details'][] = [
                    'kode_akun' => $kode_akun,
                    'debit' => $_POST['details']['debit'][$index] ?? 0,
                    'kredit' => $_POST['details']['kredit'][$index] ?? 0
                ];
            }
        }

        $result = $this->model('Jurnal')->updateJurnal($formattedData);

        if ($result > 0) {
            Flash::setFlash('Entri jurnal berhasil diperbarui.', 'success');
        } elseif ($result < 0) {
            Flash::setFlash('Gagal! Jurnal ini terkunci dan tidak dapat diubah.', 'warning');
        } else {
            Flash::setFlash('Gagal memperbarui entri jurnal.', 'danger');
        }
        header('Location: ' . BASEURL . '/jurnal');
        exit;
    }

    public function hapus($id)
    {
        if ($this->model('Jurnal')->hapusJurnal($id) > 0) {
            Flash::setFlash('Entri jurnal berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus entri jurnal. Kemungkinan jurnal ini terkunci.', 'danger');
        }
        header('Location: ' . BASEURL . '/jurnal');
        exit;
    }
    public function isPeriodClosed($tanggal) {
        $tahun = date('Y', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));

        $this->db->query("SELECT status FROM periode_akuntansi WHERE tahun = :tahun AND bulan = :bulan");
        $this->db->bind('tahun', $tahun);
        $this->db->bind('bulan', $bulan);
        
        $result = $this->db->single();
        
        return ($result && $result['status'] === 'Closed');
    }
}

