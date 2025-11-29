<?php

// Menggunakan kelas-kelas dari library PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class Akun extends Controller {

    public function index()
    {
        $data['judul'] = 'Daftar Akun';
        $akun_model = $this->model('Akun');
        $semuaAkun = $akun_model->getAllAkun();
        $data['akun'] = $this->_calculateHeaderBalances($semuaAkun);
        $this->view('templates/header', $data);
        $this->view('akun/index', $data);
        $this->view('templates/footer');
    }

    public function tambah()
    {
        $data['judul'] = 'Tambah Akun Baru';
        $this->view('templates/header', $data);
        $this->view('akun/tambah');
        $this->view('templates/footer');
    }

    public function simpan()
    {
        $akun_model = $this->model('Akun');
        $kode_akun = $_POST['kode_akun'];
        if ($akun_model->isKodeAkunExists($kode_akun)) {
            Flash::setFlash("Gagal! Kode Akun <strong>{$kode_akun}</strong> sudah digunakan.", 'danger');
            header('Location: ' . BASEURL . '/akun');
            exit;
        }
        if ($akun_model->tambahDataAkun($_POST) > 0) {
            Flash::setFlash('Data akun berhasil ditambahkan.', 'success');
        } else {
            Flash::setFlash('Gagal menambahkan data akun karena kesalahan teknis.', 'danger');
        }
        header('Location: ' . BASEURL . '/akun');
        exit;
    }

    public function edit($kode)
    {
        $data['judul'] = 'Edit Data Akun';
        $data['akun'] = $this->model('Akun')->getAkunByKode($kode);
        $this->view('templates/header', $data);
        $this->view('akun/edit', $data);
        $this->view('templates/footer');
    }

    public function update()
    {
        if ($this->model('Akun')->ubahDataAkun($_POST) > 0) {
            Flash::setFlash('Data akun berhasil diubah.', 'success');
        } else {
            Flash::setFlash('Gagal mengubah data akun.', 'danger');
        }
        header('Location: ' . BASEURL . '/akun');
        exit;
    }

    public function hapus($kode)
    {
        if ($this->model('Akun')->hapusDataAkun($kode) > 0) {
            Flash::setFlash('Data akun berhasil dihapus.', 'success');
        } else {
            Flash::setFlash('Gagal menghapus data akun.', 'danger');
        }
        header('Location: ' . BASEURL . '/akun');
        exit;
    }

    public function impor()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
            $fileTmpPath = $_FILES['file_excel']['tmp_name'];
            try {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $dataToInsert = [];

                for ($row = 2; $row <= $highestRow; $row++) {
                    $kode = $sheet->getCell('A' . $row)->getValue();
                    $nama = $sheet->getCell('B' . $row)->getValue();
                    $tipe = $sheet->getCell('C' . $row)->getValue();
                    $saldo_normal = $sheet->getCell('D' . $row)->getValue();
                    $saldo_awal = $sheet->getCell('E' . $row)->getValue();

                    if (!empty($kode) && !empty($nama)) {
                        $cleanTipe = (ucfirst(strtolower($tipe)) == 'Header') ? 'Header' : 'Detail';
                        
                        $dataToInsert[] = [
                            'kode_akun' => (string) $kode,
                            'nama_akun' => (string) $nama,
                            'tipe_akun' => $cleanTipe,
                            'posisi_saldo_normal' => (ucfirst(strtolower($saldo_normal)) == 'Debit') ? 'Debit' : 'Kredit',
                            'saldo_awal' => ($cleanTipe == 'Header') ? 0.00 : (float) $saldo_awal
                        ];
                    }
                }
                if (!empty($dataToInsert)) {
                    $this->model('Akun')->importFromExcel($dataToInsert);
                    Flash::setFlash('Data akun berhasil diimpor.', 'success');
                }
            } catch (Exception $e) {
                Flash::setFlash('Gagal mengimpor data: ' . $e->getMessage(), 'danger');
            }
        }
        header('Location: ' . BASEURL . '/akun');
        exit;
    }

    public function ekspor()
    {
        $akun_model = $this->model('Akun');
        $semuaAkun = $akun_model->getAllAkun();
        $dataAkun = $this->_calculateHeaderBalances($semuaAkun);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Daftar Akun - SIMPLE AKUNTING');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'Kode Akun');
        $sheet->setCellValue('B3', 'Nama Akun');
        $sheet->setCellValue('C3', 'Tipe');
        $sheet->setCellValue('D3', 'Saldo Normal');
        $sheet->setCellValue('E3', 'Saldo Awal');
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);

        $row = 4;
        foreach ($dataAkun as $akun) {
            $sheet->setCellValue('A' . $row, $akun['kode_akun']);
            $sheet->setCellValue('B' . $row, $akun['nama_akun']);
            $sheet->setCellValue('C' . $row, $akun['tipe_akun']);
            $sheet->setCellValue('D' . $row, $akun['posisi_saldo_normal']);
            $sheet->setCellValue('E' . $row, $akun['saldo_awal']);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Daftar_Akun_'.date('Y-m-d').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function _calculateHeaderBalances($akunList)
    {
        $balances = [];
        foreach ($akunList as $akun) {
            $balances[$akun['kode_akun']] = (float) $akun['saldo_awal'];
        }
        krsort($balances);
        foreach ($balances as $kode => $saldo) {
            $parts = explode('-', $kode);
            if (count($parts) > 1) {
                $subcode = $parts[1];
                for ($i = strlen($subcode) - 1; $i >= 0; $i--) {
                    if ($subcode[$i] != '0') {
                        $parentSubcode = substr($subcode, 0, $i) . str_repeat('0', strlen($subcode) - $i);
                        $parentKode = $parts[0] . '-' . $parentSubcode;
                        if (isset($balances[$parentKode]) && $parentKode != $kode) {
                            $balances[$parentKode] += $saldo;
                        }
                        $subcode = $parentSubcode;
                    }
                }
            }
        }
        $finalList = [];
        foreach ($akunList as $akun) {
            $processedAkun = $akun;
            if (isset($balances[$akun['kode_akun']])) {
                $processedAkun['saldo_awal'] = $balances[$akun['kode_akun']];
            }
            $finalList[] = $processedAkun;
        }
        return $finalList;
    }
}

