<?php

class Analisis extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['judul'] = 'Analisis Laporan Keuangan';
        
        $data['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? date('Y-m-01');
        $data['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? date('Y-m-t');

        // Panggil model untuk mendapatkan data rasio yang sudah dihitung
        $data['rasio'] = $this->model('Analisis')->getRasioKeuangan($data['tanggal_mulai'], $data['tanggal_selesai']);
        
        // Siapkan data lain yang dibutuhkan oleh view
        $data['periode'] = date('d M Y', strtotime($data['tanggal_mulai'])) . ' s/d ' . date('d M Y', strtotime($data['tanggal_selesai']));
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan();

        $this->view('templates/header', $data);
        $this->view('analisis/index', $data); // Memanggil file view
        $this->view('templates/footer');
    }
}

