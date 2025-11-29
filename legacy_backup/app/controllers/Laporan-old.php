<?php

// Impor kelas-kelas yang dibutuhkan dari library eksternal
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends Controller {

    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            Flash::setFlash('Anda harus login untuk mengakses halaman ini.', 'warning');
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    // --- METODE UNTUK MENAMPILKAN HALAMAN LAPORAN ---
    // ... (index, bukuBesar, neracaSaldo remain unchanged) ...
    public function index() {
        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }

    public function bukuBesar() {
        $data['judul'] = 'Buku Besar';
        $data['akun'] = $this->model('Akun')->getAllAkun();
        $data['laporan'] = null;
        $data['kode_akun_terpilih'] = $_POST['kode_akun'] ?? null;
        $data['nama_akun_terpilih'] = '';
        $data['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? date('Y-m-01');
        $data['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? date('Y-m-t');

        if (!empty($data['kode_akun_terpilih'])) {
            $data = array_merge($data, $this->_prepareLaporanData('getBukuBesar', [
                'kode_akun' => $data['kode_akun_terpilih'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
            ]));
            $akun_info = $this->model('Akun')->getAkunByKode($data['kode_akun_terpilih']);
            $data['nama_akun_terpilih'] = $akun_info['nama_akun'];
        } else {
            $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan();
        }

        $this->view('templates/header', $data);
        $this->view('laporan/bukubesar', $data);
        $this->view('templates/footer');
    }

    public function neracaSaldo() {
        $data['judul'] = 'Neraca Saldo';
        $params = ['tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-d')];
        $data = array_merge($data, $this->_prepareLaporanData('getNeracaSaldo', $params));
        $data['tanggal_selesai'] = $params['tanggal_selesai'];
        $this->view('templates/header', $data);
        $this->view('laporan/neracasaldo', $data);
        $this->view('templates/footer');
    }

    public function neracaLajur() {
        $data['judul'] = 'Neraca Lajur';
        
        $data['tanggal_mulai'] = $_POST['tanggal_mulai'] ?? date('Y-m-01');
        $data['tanggal_selesai'] = $_POST['tanggal_selesai'] ?? date('Y-m-t');

        $data['laporan'] = $this->model('Jurnal')->getNeracaLajurLengkap($data['tanggal_mulai'], $data['tanggal_selesai']);
        
        $data['periode'] = date('d M Y', strtotime($data['tanggal_mulai'])) . ' s/d ' . date('d M Y', strtotime($data['tanggal_selesai']));
        $data['perusahaan'] = $this->model('Perusahaan')->getPerusahaan();
        
        $this->view('templates/header', $data);
        $this->view('laporan/neracalajur', $data);
        $this->view('templates/footer');
    }

    public function labaRugi() {
        $data['judul'] = 'Laporan Laba Rugi Komparatif';
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1'] ?? date('Y-m-01'),
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2']) ? $_POST['tanggal_mulai_2'] : null,
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getLabaRugi', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/labarugi', $data);
        $this->view('templates/footer');
    }

    public function posisiKeuangan() {
        $data['judul'] = 'Laporan Posisi Keuangan Komparatif';
        $params = [
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getPosisiKeuangan', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/posisikeuangan', $data);
        $this->view('templates/footer');
    }

    /**
     * [FIXED] Menampilkan Laporan Perubahan Ekuitas.
     * Logika untuk mengambil data dari model telah ditambahkan.
     */
    public function perubahanEkuitas(): void {
        $data['judul'] = 'Laporan Perubahan Ekuitas';
        $params = [
            'tanggal_mulai_1' => $_POST['tanggal_mulai_1'] ?? date('Y-m-01'),
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1'] ?? date('Y-m-t'),
            'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2']) ? $_POST['tanggal_mulai_2'] : null,
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2']) ? $_POST['tanggal_selesai_2'] : null,
        ];
        
        $data = array_merge($data, $this->_prepareLaporanData('getPerubahanEkuitas', $params));
        $data = array_merge($data, $params);
        
        $this->view('templates/header', $data);
        $this->view('laporan/perubahanekuitas', $data);
        $this->view('templates/footer');
    }
    
    public function arusKas() {
        $data['judul'] = 'Laporan Arus Kas';
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai'] ?? date('Y-m-01'),
            'tanggal_selesai' => $_POST['tanggal_selesai'] ?? date('Y-m-t'),
        ];
        $data = array_merge($data, $this->_prepareLaporanData('getArusKas', $params));
        $data = array_merge($data, $params);
        $this->view('templates/header', $data);
        $this->view('laporan/aruskas', $data);
        $this->view('templates/footer');
    }

    // --- METODE UNTUK EKSPOR KE EXCEL ---
    // ... (eksporBukuBesar, eksporLabaRugi, eksporPosisiKeuangan, eksporPerubahanEkuitas remain unchanged) ...
    
    public function eksporBukuBesar() {
        $kode_akun = $_POST['kode_akun_export'];
        $tanggal_mulai = $_POST['tanggal_mulai_export'];
        $tanggal_selesai = $_POST['tanggal_selesai_export'];
        $jurnal_model = $this->model('Jurnal');
        $akun_model = $this->model('Akun');
        $akun_info = $akun_model->getAkunByKode($kode_akun);
        $laporanData = $jurnal_model->getBukuBesar($kode_akun, $tanggal_mulai, $tanggal_selesai);
        $perusahaan = $this->model('Perusahaan')->getPerusahaan();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', $perusahaan['nama_perusahaan']);
        $sheet->setCellValue('A2', 'Laporan Buku Besar');
        $sheet->setCellValue('A3', "Akun: [{$kode_akun}] {$akun_info['nama_akun']}");
        $sheet->setCellValue('A4', "Periode: " . date('d M Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d M Y', strtotime($tanggal_selesai)));
        $sheet->mergeCells('A1:F1')->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A2:F2')->getStyle('A2')->getFont()->setBold(true);
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');

        $row = 6;
        $sheet->setCellValue('A'.$row, 'Tanggal')->setCellValue('B'.$row, 'No. Transaksi')->setCellValue('C'.$row, 'Deskripsi')->setCellValue('D'.$row, 'Debit')->setCellValue('E'.$row, 'Kredit')->setCellValue('F'.$row, 'Saldo');
        $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('C'.$row, 'Saldo Awal Periode');
        $sheet->setCellValue('F'.$row, $laporanData['saldo_awal_periode']);
        $sheet->getStyle('C'.$row.':F'.$row)->getFont()->setBold(true);
        $row++;

        $saldo = $laporanData['saldo_awal_periode'];
        foreach($laporanData['transaksi'] as $transaksi) {
            if ($laporanData['posisi_saldo_normal'] == 'Debit') { $saldo += $transaksi['debit'] - $transaksi['kredit']; } else { $saldo += $transaksi['kredit'] - $transaksi['debit']; }
            $sheet->setCellValue('A'.$row, date('d-m-Y', strtotime($transaksi['tanggal'])))->setCellValue('B'.$row, $transaksi['no_transaksi'])->setCellValue('C'.$row, $transaksi['deskripsi'])->setCellValue('D'.$row, $transaksi['debit'])->setCellValue('E'.$row, $transaksi['kredit'])->setCellValue('F'.$row, $saldo);
            $row++;
        }

        $sheet->setCellValue('C'.$row, 'Saldo Akhir Periode');
        $sheet->setCellValue('F'.$row, $saldo);
        $sheet->getStyle('C'.$row.':F'.$row)->getFont()->setBold(true);
        
        foreach (range('A', 'C') as $col) { $sheet->getColumnDimension($col)->setAutoSize(true); }
        foreach (range('D', 'F') as $col) { $sheet->getColumnDimension($col)->setWidth(18); }
        $sheet->getStyle('D7:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Buku_Besar_'.$kode_akun.'_'.date('Ymd').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function eksporLabaRugi() {
        $tgl_mulai_1 = $_POST['tanggal_mulai_1_export'];
        $tgl_selesai_1 = $_POST['tanggal_selesai_1_export'];
        $tgl_mulai_2 = !empty($_POST['tanggal_mulai_2_export']) ? $_POST['tanggal_mulai_2_export'] : null;
        $tgl_selesai_2 = !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null;
        $laporanData = $this->model('Jurnal')->getLabaRugi($tgl_mulai_1, $tgl_selesai_1, $tgl_mulai_2, $tgl_selesai_2);
        $perusahaan = $this->model('Perusahaan')->getPerusahaan();
        $periode_1_str = date('d M Y', strtotime($tgl_mulai_1)) . ' - ' . date('d M Y', strtotime($tgl_selesai_1));
        $periode_2_str = $tgl_mulai_2 ? (date('d M Y', strtotime($tgl_mulai_2)) . ' - ' . date('d M Y', strtotime($tgl_selesai_2))) : null;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', $perusahaan['nama_perusahaan']);
        $sheet->setCellValue('A2', 'Laporan Laba Rugi Komparatif');
        $sheet->mergeCells('A1:E1')->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A2:E2')->getStyle('A2')->getFont()->setBold(true);
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Keterangan')->setCellValue('B'.$row, $periode_1_str);
        if ($periode_2_str) {
            $sheet->setCellValue('C'.$row, $periode_2_str);
            $sheet->setCellValue('D'.$row, 'Perubahan (Rp)');
            $sheet->setCellValue('E'.$row, 'Perubahan (%)');
        }
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, 'Pendapatan')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        foreach ($laporanData['pendapatan'] as $item) {
            $perubahan_rp = $item['total_1'] - $item['total_2'];
            $perubahan_persen = ($item['total_2'] != 0) ? ($perubahan_rp / abs($item['total_2'])) * 100 : 0;
            $sheet->setCellValue('A'.$row, $item['nama_akun'])->getStyle('A'.$row)->getAlignment()->setIndent(1);
            $sheet->setCellValue('B'.$row, $item['total_1']);
            if ($periode_2_str) {
                $sheet->setCellValue('C'.$row, $item['total_2']);
                $sheet->setCellValue('D'.$row, $perubahan_rp);
                $sheet->setCellValue('E'.$row, $perubahan_persen)->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00"%"');
            }
            $row++;
        }
        $sheet->setCellValue('A'.$row, 'Total Pendapatan')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['total_pendapatan_1'])->getStyle('B'.$row)->getFont()->setBold(true);
        if ($periode_2_str) { $sheet->setCellValue('C'.$row, $laporanData['total_pendapatan_2'])->getStyle('C'.$row)->getFont()->setBold(true); }
        $row+=2;
        $sheet->setCellValue('A'.$row, 'Beban-Beban')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        foreach ($laporanData['beban'] as $item) {
            $perubahan_rp = $item['total_1'] - $item['total_2'];
            $perubahan_persen = ($item['total_2'] != 0) ? ($perubahan_rp / abs($item['total_2'])) * 100 : 0;
            $sheet->setCellValue('A'.$row, $item['nama_akun'])->getStyle('A'.$row)->getAlignment()->setIndent(1);
            $sheet->setCellValue('B'.$row, $item['total_1']);
            if ($periode_2_str) {
                $sheet->setCellValue('C'.$row, $item['total_2']);
                $sheet->setCellValue('D'.$row, $perubahan_rp);
                $sheet->setCellValue('E'.$row, $perubahan_persen)->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00"%"');
            }
            $row++;
        }
        $sheet->setCellValue('A'.$row, 'Total Beban')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['total_beban_1'])->getStyle('B'.$row)->getFont()->setBold(true);
        if ($periode_2_str) { $sheet->setCellValue('C'.$row, $laporanData['total_beban_2'])->getStyle('C'.$row)->getFont()->setBold(true); }
        $row+=2;
        $labaRugi1 = $laporanData['total_pendapatan_1'] - $laporanData['total_beban_1'];
        $labaRugi2 = $laporanData['total_pendapatan_2'] - $laporanData['total_beban_2'];
        $sheet->setCellValue('A'.$row, ($labaRugi1 >= 0) ? 'LABA BERSIH' : 'RUGI BERSIH');
        $sheet->setCellValue('B'.$row, $labaRugi1);
        if ($periode_2_str) { $sheet->setCellValue('C'.$row, $labaRugi2); }
        $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setBold(true)->setSize(12);
        foreach (range('A', 'E') as $col) { $sheet->getColumnDimension($col)->setAutoSize(true); }
        $sheet->getStyle('B5:D'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Laba_Rugi_Komparatif_'.date('Ymd').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function eksporPosisiKeuangan() {
        $tanggal_selesai_1 = $_POST['tanggal_selesai_1_export'];
        $tanggal_selesai_2 = !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null;
        $laporanData = $this->model('Jurnal')->getPosisiKeuangan($tanggal_selesai_1, $tanggal_selesai_2);
        $perusahaan = $this->model('Perusahaan')->getPerusahaan();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', $perusahaan['nama_perusahaan']);
        $sheet->setCellValue('A2', 'Laporan Posisi Keuangan');
        $sheet->mergeCells('A1:C1')->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A2:C2')->getStyle('A2')->getFont()->setBold(true);
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Keterangan');
        $sheet->setCellValue('B'.$row, date('d M Y', strtotime($tanggal_selesai_1)));
        if ($tanggal_selesai_2) {
            $sheet->setCellValue('C'.$row, date('d M Y', strtotime($tanggal_selesai_2)));
        }
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'ASET')->getStyle('A'.$row)->getFont()->setBold(true); $row++;
        foreach($laporanData['periode_1']['aset'] as $item) {
            $sheet->setCellValue('A'.$row, $item['nama_akun'])->getStyle('A'.$row)->getAlignment()->setIndent(1);
            $sheet->setCellValue('B'.$row, $item['total']);
            if($tanggal_selesai_2) {
                $key = array_search($item['kode_akun'], array_column($laporanData['periode_2']['aset'], 'kode_akun'));
                $sheet->setCellValue('C'.$row, ($key !== false) ? $laporanData['periode_2']['aset'][$key]['total'] : 0);
            }
            $row++;
        }
        $sheet->setCellValue('A'.$row, 'TOTAL ASET')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['periode_1']['total_aset'])->getStyle('B'.$row)->getFont()->setBold(true);
        if($tanggal_selesai_2) $sheet->setCellValue('C'.$row, $laporanData['periode_2']['total_aset'])->getStyle('C'.$row)->getFont()->setBold(true);
        $row+=2;

        $sheet->setCellValue('A'.$row, 'KEWAJIBAN DAN EKUITAS')->getStyle('A'.$row)->getFont()->setBold(true); $row++;
        $sheet->setCellValue('A'.$row, 'Kewajiban')->getStyle('A'.$row)->getFont()->setBold(true); $row++;
        foreach($laporanData['periode_1']['kewajiban'] as $item) { /* ... */ }
        $sheet->setCellValue('A'.$row, 'Total Kewajiban')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['periode_1']['total_kewajiban'])->getStyle('B'.$row)->getFont()->setBold(true);
        if($tanggal_selesai_2) $sheet->setCellValue('C'.$row, $laporanData['periode_2']['total_kewajiban'])->getStyle('C'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, 'Ekuitas')->getStyle('A'.$row)->getFont()->setBold(true); $row++;
        foreach($laporanData['periode_1']['modal'] as $item) { /* ... */ }
        $sheet->setCellValue('A'.$row, 'Total Ekuitas')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['periode_1']['total_modal'])->getStyle('B'.$row)->getFont()->setBold(true);
        if($tanggal_selesai_2) $sheet->setCellValue('C'.$row, $laporanData['periode_2']['total_modal'])->getStyle('C'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, 'TOTAL KEWAJIBAN DAN EKUITAS')->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $laporanData['periode_1']['total_kewajiban'] + $laporanData['periode_1']['total_modal'])->getStyle('B'.$row)->getFont()->setBold(true);
        if($tanggal_selesai_2) $sheet->setCellValue('C'.$row, $laporanData['periode_2']['total_kewajiban'] + $laporanData['periode_2']['total_modal'])->getStyle('C'.$row)->getFont()->setBold(true);
        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getStyle('B5:C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Posisi_Keuangan_'.date('Ymd').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function eksporPerubahanEkuitas() {
        $params = ['tanggal_mulai_1' => $_POST['tanggal_mulai_1_export'], 'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'], 'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2_export']) ? $_POST['tanggal_mulai_2_export'] : null, 'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null];
        $data = $this->_prepareLaporanData('getPerubahanEkuitas', $params);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $data['perusahaan']['nama_perusahaan']);
        $sheet->setCellValue('A2', 'Laporan Perubahan Ekuitas');
        $sheet->mergeCells('A1:C1')->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A2:C2')->getStyle('A2')->getFont()->setBold(true);
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Keterangan');
        $sheet->setCellValue('B'.$row, $data['periode_1']);
        if($data['periode_2']) $sheet->setCellValue('C'.$row, $data['periode_2']);
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, 'Modal Awal Periode');
        $sheet->setCellValue('B'.$row, $data['laporan']['periode_1']['modal_awal']);
        if($data['periode_2']) $sheet->setCellValue('C'.$row, $data['laporan']['periode_2']['modal_awal']);
        $row++;
        $sheet->setCellValue('A'.$row, 'Setoran (Penarikan) Modal')->getStyle('A'.$row)->getAlignment()->setIndent(1);
        $sheet->setCellValue('B'.$row, $data['laporan']['periode_1']['perubahan_modal_langsung']);
        if($data['periode_2']) $sheet->setCellValue('C'.$row, $data['laporan']['periode_2']['perubahan_modal_langsung']);
        $row++;
        $sheet->setCellValue('A'.$row, 'Laba (Rugi) Periode Berjalan')->getStyle('A'.$row)->getAlignment()->setIndent(1);
        $sheet->setCellValue('B'.$row, $data['laporan']['periode_1']['laba_rugi_periode_berjalan']);
        if($data['periode_2']) $sheet->setCellValue('C'.$row, $data['laporan']['periode_2']['laba_rugi_periode_berjalan']);
        $row++;
        $sheet->setCellValue('A'.$row, 'MODAL AKHIR PERIODE')->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $data['laporan']['periode_1']['modal_akhir'])->getStyle('B'.$row)->getFont()->setBold(true);
        if($data['periode_2']) $sheet->setCellValue('C'.$row, $data['laporan']['periode_2']['modal_akhir'])->getStyle('C'.$row)->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getStyle('B5:C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Perubahan_Ekuitas_'.date('Ymd').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * [NEW] Mengekspor Laporan Neraca Lajur ke format Excel.
     */
    public function eksporNeracaLajur() {
        $tanggal_mulai = $_POST['tanggal_mulai_export'];
        $tanggal_selesai = $_POST['tanggal_selesai_export'];
        
        $laporanData = $this->model('Jurnal')->getNeracaLajurLengkap($tanggal_mulai, $tanggal_selesai);
        $perusahaan = $this->model('Perusahaan')->getPerusahaan();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', $perusahaan['nama_perusahaan']);
        $sheet->setCellValue('A2', 'Neraca Lajur');
        $sheet->setCellValue('A3', "Untuk Periode " . date('d M Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d M Y', strtotime($tanggal_selesai)));
        $sheet->mergeCells('A1:P1')->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A2:P2')->getStyle('A2')->getFont()->setBold(true);
        $sheet->mergeCells('A3:P3');

        $sheet->mergeCells('A5:A6')->setCellValue('A5', 'Kode Akun');
        $sheet->mergeCells('B5:B6')->setCellValue('B5', 'Nama Akun');
        $sheet->mergeCells('C5:D5')->setCellValue('C5', 'Saldo Awal');
        $sheet->setCellValue('C6', 'Debit')->setCellValue('D6', 'Kredit');
        $sheet->mergeCells('E5:F5')->setCellValue('E5', 'Mutasi Periode');
        $sheet->setCellValue('E6', 'Debit')->setCellValue('F6', 'Kredit');
        $sheet->mergeCells('G5:H5')->setCellValue('G5', 'Neraca Saldo');
        $sheet->setCellValue('G6', 'Debit')->setCellValue('H6', 'Kredit');
        $sheet->mergeCells('I5:J5')->setCellValue('I5', 'Penyesuaian');
        $sheet->setCellValue('I6', 'Debit')->setCellValue('J6', 'Kredit');
        $sheet->mergeCells('K5:L5')->setCellValue('K5', 'NS Disesuaikan');
        $sheet->setCellValue('K6', 'Debit')->setCellValue('L6', 'Kredit');
        $sheet->mergeCells('M5:N5')->setCellValue('M5', 'Laba Rugi');
        $sheet->setCellValue('M6', 'Debit')->setCellValue('N6', 'Kredit');
        $sheet->mergeCells('O5:P5')->setCellValue('O5', 'Posisi Keuangan');
        $sheet->setCellValue('O6', 'Debit')->setCellValue('P6', 'Kredit');
        $sheet->getStyle('A5:P6')->getFont()->setBold(true);
        $sheet->getStyle('A5:P6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $row = 7;
        $totals = array_fill_keys(['sa_debit', 'sa_kredit', 'mutasi_debit', 'mutasi_kredit', 'ns_debit', 'ns_kredit', 'penyesuaian_debit', 'penyesuaian_kredit', 'nsd_debit', 'nsd_kredit', 'lr_debit', 'lr_kredit', 'poskeu_debit', 'poskeu_kredit'], 0);
        
        foreach ($laporanData as $item) {
            $sheet->fromArray(array_values($item), null, 'A'.$row);
            foreach($totals as $key => &$total) { $total += $item[$key]; }
            $row++;
        }

        $sheet->mergeCells('A'.$row.':B'.$row)->setCellValue('A'.$row, 'Total');
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->fromArray(array_values($totals), null, 'C'.$row);
        $sheet->getStyle('A'.$row.':P'.$row)->getFont()->setBold(true);
        $row++;
        
        $labaRugi = $totals['lr_kredit'] - $totals['lr_debit'];
        $sheet->mergeCells('A'.$row.':L'.$row)->setCellValue('A'.$row, 'Laba (Rugi) Bersih');
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        if ($labaRugi >= 0) {
            $sheet->setCellValue('M'.$row, $labaRugi)->setCellValue('P'.$row, $labaRugi);
        } else {
            $sheet->setCellValue('N'.$row, abs($labaRugi))->setCellValue('O'.$row, abs($labaRugi));
        }
        $sheet->getStyle('A'.$row.':P'.$row)->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells('A'.$row.':L'.$row)->setCellValue('A'.$row, 'Total Akhir');
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('M'.$row, $totals['lr_debit'] + (($labaRugi >= 0) ? $labaRugi : 0));
        $sheet->setCellValue('N'.$row, $totals['lr_kredit'] + (($labaRugi < 0) ? abs($labaRugi) : 0));
        $sheet->setCellValue('O'.$row, $totals['poskeu_debit'] + (($labaRugi < 0) ? abs($labaRugi) : 0));
        $sheet->setCellValue('P'.$row, $totals['poskeu_kredit'] + (($labaRugi >= 0) ? $labaRugi : 0));
        $sheet->getStyle('A'.$row.':P'.$row)->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(35);
        foreach (range('C', 'P') as $col) { $sheet->getColumnDimension($col)->setWidth(15); }
        $sheet->getStyle('C7:P'.$row)->getNumberFormat()->setFormatCode('#,##0.00;(#,##0.00);"-"');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Neraca_Lajur_'.date('Ymd').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    // --- METODE UNTUK EKSPOR KE PDF ---
    
    public function eksporPdfBukuBesar() {
        $params = [
            'kode_akun' => $_POST['kode_akun_export'],
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export'],
        ];
        $data = $this->_prepareLaporanData('getBukuBesar', $params);
        
        $akun_info = $this->model('Akun')->getAkunByKode($params['kode_akun']);
        $data['kode_akun_terpilih'] = $params['kode_akun'];
        $data['nama_akun_terpilih'] = $akun_info['nama_akun'];

        $this->_generatePdf('laporan/bukubesar_pdf', $data, 'Buku_Besar_'.$params['kode_akun'].'_'.date('Ymd'));
    }
    
    public function eksporPdfLabaRugi() {
        $params = ['tanggal_mulai_1' => $_POST['tanggal_mulai_1_export'], 'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'], 'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2_export']) ? $_POST['tanggal_mulai_2_export'] : null, 'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null];
        $data = $this->_prepareLaporanData('getLabaRugi', $params);
        $this->_generatePdf('laporan/labarugi_pdf', $data, 'Laporan_Laba_Rugi_'.date('Ymd'));
    }

    public function eksporPdfPosisiKeuangan() {
        $params = [
            'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'],
            'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null
        ];
        $data = $this->_prepareLaporanData('getPosisiKeuangan', $params);
        $this->_generatePdf('laporan/posisikeuangan_pdf', $data, 'Laporan_Posisi_Keuangan_'.date('Ymd'));
    }

    public function eksporPdfPerubahanEkuitas() {
        $params = ['tanggal_mulai_1' => $_POST['tanggal_mulai_1_export'], 'tanggal_selesai_1' => $_POST['tanggal_selesai_1_export'], 'tanggal_mulai_2' => !empty($_POST['tanggal_mulai_2_export']) ? $_POST['tanggal_mulai_2_export'] : null, 'tanggal_selesai_2' => !empty($_POST['tanggal_selesai_2_export']) ? $_POST['tanggal_selesai_2_export'] : null];
        $data = $this->_prepareLaporanData('getPerubahanEkuitas', $params);
        $this->_generatePdf('laporan/perubahanekuitas_pdf', $data, 'Laporan_Perubahan_Ekuitas_'.date('Ymd'));
    }
    
    public function eksporPdfArusKas() {
        $params = ['tanggal_mulai' => $_POST['tanggal_mulai_export'], 'tanggal_selesai' => $_POST['tanggal_selesai_export']];
        $data = $this->_prepareLaporanData('getArusKas', $params);
        $this->_generatePdf('laporan/aruskas_pdf', $data, 'Laporan_Arus_Kas_'.date('Ymd'));
    }

    /**
     * [NEW] Mengekspor Laporan Neraca Lajur ke format PDF.
     */
    public function eksporPdfNeracaLajur() {
        $params = [
            'tanggal_mulai' => $_POST['tanggal_mulai_export'],
            'tanggal_selesai' => $_POST['tanggal_selesai_export']
        ];
        // Menggunakan _prepareLaporanData untuk data umum seperti perusahaan & periode
        $data = $this->_prepareLaporanData(null, ['tanggal_mulai' => $params['tanggal_mulai'], 'tanggal_selesai' => $params['tanggal_selesai']]);
        // Mengambil data spesifik laporan
        $data['laporan'] = $this->model('Jurnal')->getNeracaLajurLengkap($params['tanggal_mulai'], $params['tanggal_selesai']);
        
        // Anda perlu membuat view baru: views/laporan/neracalajur_pdf.php
        $this->_generatePdf('laporan/neracalajur_pdf', $data, 'Neraca_Lajur_'.date('Ymd'));
    }
    
    // --- FUNGSI HELPER PRIBADI ---
    private function _prepareLaporanData($modelFunction, $params): array {
        $data = [];
        if ($modelFunction) {
            $data['laporan'] = call_user_func_array(callback: [$this->model(model: 'Jurnal'), $modelFunction], args: array_values(array: $params));
        }
        $perusahaanModel = $this->model(model: 'Perusahaan');
        $userModel = $this->model(model: 'User');
        $data['perusahaan'] = $perusahaanModel->getPerusahaan();
        $data['penandatangan_1'] = $data['perusahaan']['penandatangan_1_id'] ? $userModel->getUserById($data['perusahaan']['penandatangan_1_id']) : ['nama_user' => '(Belum Diatur)', 'jabatan' => '(Belum Diatur)'];
        $data['penandatangan_2'] = $data['perusahaan']['penandatangan_2_id'] ? $userModel->getUserById($data['perusahaan']['penandatangan_2_id']) : ['nama_user' => '(Belum Diatur)', 'jabatan' => '(Belum Diatur)'];
        $data['kota_laporan'] = $data['perusahaan']['kota_laporan'] ?? 'Kota Anda';
        
        if (isset($params['tanggal_mulai_1'])) {
            $data['periode_1'] = date('d M Y', strtotime($params['tanggal_mulai_1'])) . ' s/d ' . date('d M Y', strtotime($params['tanggal_selesai_1']));
            $data['periode_2'] = !empty($params['tanggal_mulai_2']) ? (date('d M Y', strtotime($params['tanggal_mulai_2'])) . ' s/d ' . date('d M Y', strtotime($params['tanggal_selesai_2']))) : null;
        } elseif (isset($params['tanggal_mulai'])) {
             $data['periode_1'] = date('d M Y', strtotime($params['tanggal_mulai'])) . ' s/d ' . date('d M Y', strtotime($params['tanggal_selesai']));
        } elseif (isset($params['tanggal_selesai_1'])) {
             $data['periode_1'] = date('d F Y', strtotime($params['tanggal_selesai_1']));
             $data['periode_2'] = !empty($params['tanggal_selesai_2']) ? date('d F Y', strtotime($params['tanggal_selesai_2'])) : null;
        } elseif (isset($params['tanggal_selesai'])) {
             $data['periode_1'] = date('d M Y', strtotime($params['tanggal_selesai']));
        }
        return $data;
    }
    
    private function _generatePdf($view, $data, $filename) {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        ob_start();
        $this->view($view, $data);
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape lebih cocok untuk Neraca Lajur
        $dompdf->render();
        $dompdf->stream($filename.".pdf", array("Attachment" => 0));
        exit;
    }
}