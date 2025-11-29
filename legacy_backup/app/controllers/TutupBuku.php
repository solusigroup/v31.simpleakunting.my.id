<?php

class TutupBuku extends Controller {
    public function __construct() {
        parent::__construct();
        // **PERBAIKAN: Izinkan akses untuk Admin DAN Manajer**
        if (!Auth::isAdmin() && !Auth::isManager()) {
            Flash::setFlash('Hanya Admin atau Manajer yang dapat mengakses halaman ini.', 'danger');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
    }

    public function index() {
        $data['judul'] = 'Tutup Buku Akhir Periode';
        
        $tutupBukuModel = $this->model('TutupBuku');
        $latestClosed = $tutupBukuModel->getLatestClosedPeriod();
        
        $nextMonthPeriod = $latestClosed ? date('Y-m', strtotime($latestClosed['tahun'].'-'.$latestClosed['bulan'].'-01' . ' +1 month')) : date('Y-m', strtotime('first day of last month'));
        $data['periode_bulanan_next'] = $nextMonthPeriod;
        
        $data['preview'] = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipe_proses'])) {
            $data['tipe_proses'] = $_POST['tipe_proses'];
            $data['periode_raw'] = $_POST['periode']; // YYYY-MM untuk bulanan, YYYY untuk tahunan

            if ($data['tipe_proses'] == 'Bulanan') {
                $data['periode_label'] = date('F Y', strtotime($data['periode_raw'].'-01'));
                $data['preview'] = $tutupBukuModel->getClosingJournalPreview($data['periode_raw']);
            } else { // Tahunan
                $data['periode_label'] = 'Tahun ' . $data['periode_raw'];
                $data['preview'] = $tutupBukuModel->getClosingJournalPreview($data['periode_raw'], true);
            }
        }
        
        $this->view('templates/header', $data);
        $this->view('tutupbuku/index', $data);
        $this->view('templates/footer');
    }
    
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['periode'])) {
            $tutupBukuModel = $this->model('TutupBuku');
            if ($tutupBukuModel->prosesTutupBuku($_POST['periode'], $_POST['tipe_proses'])) {
                Flash::setFlash('Proses tutup buku berhasil.', 'success');
            }
        }
        header('Location: ' . BASEURL . '/jurnal');
        exit;
    }
}

