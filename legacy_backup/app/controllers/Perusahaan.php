<?php

class Perusahaan extends Controller {

    /**
     * Menampilkan halaman utama Pengaturan Perusahaan.
     * Versi ini juga mengambil daftar pengguna dan daftar akun untuk dropdown.
     */
    public function index() {
        $data['judul'] = 'Pengaturan Perusahaan';
        // Ambil data perusahaan saat ini
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan();
        // Ambil SEMUA pengguna untuk mengisi pilihan penandatangan
        $data['users'] = $this->model('User')->getAllUsers();
        // **PERUBAHAN: Ambil SEMUA akun untuk mengisi pilihan akun kontrol**
        $data['akun'] = $this->model('Akun')->getAllAkun();
        
        $this->view('templates/header', $data);
        $this->view('perusahaan/index', $data);
        $this->view('templates/footer');
    }

    /**
     * Memproses pembaruan data perusahaan, termasuk upload logo.
     */
    public function update() {
        $logo_path = null;
        
        // Cek apakah ada file logo baru yang diunggah dan valid
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['logo'];
            $target_dir = "img/logos/"; // Pastikan folder public/img/logos ada
            
            // Buat nama file yang unik untuk menghindari penimpaan file
            $target_file = $target_dir . uniqid() . '_' . basename($file["name"]);

            // Pastikan direktori tujuan ada, jika tidak, buat
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Pindahkan file yang diunggah ke direktori tujuan
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $logo_path = $target_file;
            } else {
                Flash::setFlash('Gagal mengunggah file logo.', 'danger');
                header('Location: ' . BASEURL . '/perusahaan');
                exit;
            }
        }

        // Panggil model untuk memperbarui data di database
        if ($this->model('Perusahaan')->updatePerusahaan($_POST, $logo_path) > 0) {
            Flash::setFlash('Data perusahaan berhasil diperbarui.', 'success');
        } else {
            // Jika tidak ada baris yang terpengaruh, berarti tidak ada perubahan data
            Flash::setFlash('Tidak ada perubahan data yang disimpan.', 'info');
        }
        header('Location: ' . BASEURL . '/perusahaan');
        exit;
    }
}

