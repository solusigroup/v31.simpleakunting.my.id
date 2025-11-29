<?php

class Penyesuaian extends Controller {

    /**
     * Constructor ini berjalan secara otomatis dan berfungsi sebagai gerbang keamanan.
     */
    public function __construct() {
        parent::__construct();
        // Proteksi: Hanya Admin atau Manajer yang boleh melakukan penyesuaian
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Anda tidak memiliki hak akses untuk halaman ini.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    /**
     * Menampilkan halaman utama Jurnal Penyesuaian dan pratinjau depresiasi.
     */
    public function index() {
        $data['judul'] = 'Jurnal Penyesuaian';
        
        // Atur periode default ke bulan sebelumnya atau sesuai input form
        $data['periode'] = $_POST['periode'] ?? date('Y-m', strtotime('first day of last month'));
        $data['preview'] = null;

        // Jika form disubmit untuk melihat pratinjau
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['preview'])) {
            $asetModel = $this->model('Aset');
            $data['preview'] = $asetModel->getDepreciationData($data['periode']);
        }
        
        $this->view('templates/header', $data);
        $this->view('penyesuaian/index', $data);
        $this->view('templates/footer');
    }
    
    /**
     * Memproses dan membuat jurnal depresiasi otomatis.
     */
    public function prosesDepresiasi() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['periode'])) {
            $periode = $_POST['periode'];
            $asetModel = $this->model('Aset');
            
            // Ambil kembali data penyusutan untuk diproses
            $depreciationData = $asetModel->getDepreciationData($periode);

            if ($asetModel->runDepreciationJournal($periode, $depreciationData)) {
                Flash::setFlash('Jurnal penyesuaian depresiasi berhasil dibuat.', 'success');
            } else {
                Flash::setFlash('Gagal membuat jurnal penyesuaian.', 'danger');
            }
        }
        // Arahkan ke Jurnal Umum untuk melihat hasilnya
        header('Location: ' . BASEURL . '/jurnal');
        exit;
    }
}

