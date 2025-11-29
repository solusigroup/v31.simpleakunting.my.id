<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class Persediaan extends Controller {
    public function index() {
        $data['judul'] = 'Master Persediaan';
        $data['barang'] = $this->model('Persediaan')->getAllBarang();
        $this->view('templates/header', $data);
        $this->view('persediaan/index', $data);
        $this->view('templates/footer');
    }

    public function tambah() {
        $data['judul'] = 'Tambah Barang Baru';
        $data['akun'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('persediaan/tambah', $data);
        $this->view('templates/footer');
    }

    public function simpan() {
        if ($this->model('Persediaan')->isKodeBarangExists($_POST['kode_barang'])) {
            Flash::setFlash('Gagal! Kode barang sudah digunakan.', 'danger');
            header('Location: ' . BASEURL . '/persediaan/tambah');
            exit;
        }

        if ($this->model('Persediaan')->tambahDataBarang($_POST) > 0) {
            Flash::setFlash('Data barang berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan data barang.', 'danger');
        }
        header('Location: ' . BASEURL . '/persediaan');
        exit;
    }

    public function edit($id) {
        $data['judul'] = 'Edit Barang Persediaan';
        $data['barang'] = $this->model('Persediaan')->getBarangById($id);
        $data['akun'] = $this->model('Akun')->getAllAkun();
        $this->view('templates/header', $data);
        $this->view('persediaan/edit', $data);
        $this->view('templates/footer');
    }

    public function update() {
        if ($this->model('Persediaan')->ubahDataBarang($_POST) > 0) {
            Flash::setFlash('Data barang berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data barang.', 'danger');
        }
        header('Location: ' . BASEURL . '/persediaan');
        exit;
    }

    public function hapus($id) {
        if ($this->model('Persediaan')->hapusDataBarang($id) > 0) {
            Flash::setFlash('Data barang berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data barang.', 'danger');
        }
        header('Location: ' . BASEURL . '/persediaan');
        exit;
    }
    
    /**
     * FUNGSI YANG DIPERBARUI: Memproses file Excel yang di-upload untuk diimpor.
     * Sekarang membaca kolom Stok Awal.
     */
    public function impor()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
            $fileTmpPath = $_FILES['file_excel']['tmp_name'];
            try {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $dataToInsert = [];

                // Loop dari baris ke-2 untuk melewati header
                for ($row = 2; $row <= $highestRow; $row++) {
                    $dataToInsert[] = [
                        'kode_barang' => $sheet->getCell('A' . $row)->getValue(),
                        'nama_barang' => $sheet->getCell('B' . $row)->getValue(),
                        'satuan' => $sheet->getCell('C' . $row)->getValue(),
                        'stok_awal' => $sheet->getCell('D' . $row)->getValue(), // Baca kolom Stok Awal
                        'harga_beli' => $sheet->getCell('E' . $row)->getValue(),
                        'harga_jual' => $sheet->getCell('F' . $row)->getValue(),
                        'akun_persediaan' => $sheet->getCell('G' . $row)->getValue(),
                        'akun_hpp' => $sheet->getCell('H' . $row)->getValue(),
                        'akun_penjualan' => $sheet->getCell('I' . $row)->getValue(),
                    ];
                }
                if (!empty($dataToInsert)) {
                    $this->model('Persediaan')->importFromExcel($dataToInsert);
                    Flash::setFlash('Data persediaan berhasil diimpor.', 'success');
                }
            } catch (Exception $e) {
                Flash::setFlash('Gagal mengimpor data: ' . $e->getMessage(), 'danger');
            }
        }
        header('Location: ' . BASEURL . '/persediaan');
        exit;
    }

    /**
     * FUNGSI YANG DIPERBARUI: Membuat dan mengunduh file Excel dari daftar persediaan saat ini.
     * Sekarang menyertakan kolom Stok Awal dan Nilai Persediaan.
     */
    public function ekspor()
    {
        $dataBarang = $this->model('Persediaan')->getAllBarang();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Master Data Persediaan');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'Kode Barang')->setCellValue('B3', 'Nama Barang')->setCellValue('C3', 'Satuan')
              ->setCellValue('D3', 'Stok Awal')->setCellValue('E3', 'Stok Terkini')->setCellValue('F3', 'Harga Beli')
              ->setCellValue('G3', 'Nilai Persediaan')->setCellValue('H3', 'Akun Persediaan')
              ->setCellValue('I3', 'Akun HPP')->setCellValue('J3', 'Akun Penjualan');
        $sheet->getStyle('A3:J3')->getFont()->setBold(true);

        $row = 4;
        foreach ($dataBarang as $brg) {
            $nilaiPersediaan = ($brg['stok_saat_ini'] ?? 0) * ($brg['harga_beli'] ?? 0);
            $sheet->setCellValue('A' . $row, $brg['kode_barang']);
            $sheet->setCellValue('B' . $row, $brg['nama_barang']);
            $sheet->setCellValue('C' . $row, $brg['satuan']);
            $sheet->setCellValue('D' . $row, $brg['stok_awal']);
            $sheet->setCellValue('E' . $row, $brg['stok_saat_ini']);
            $sheet->setCellValue('F' . $row, $brg['harga_beli']);
            $sheet->setCellValue('G' . $row, $nilaiPersediaan);
            $sheet->setCellValue('H' . $row, $brg['akun_persediaan']);
            $sheet->setCellValue('I' . $row, $brg['akun_hpp']);
            $sheet->setCellValue('J' . $row, $brg['akun_penjualan']);

            // Formatting angka
            $sheet->getStyle('D' . $row . ':G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Master_Persediaan_'.date('Y-m-d').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

