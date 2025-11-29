<?php

// Muat semua model yang dibutuhkan secara manual
require_once 'Jurnal_model.php';
require_once 'Persediaan_model.php';

class Penjualan_model {
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPenjualan() {
        $this->db->query("SELECT p.*, pl.nama_pelanggan 
                         FROM penjualan p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                         ORDER BY p.tanggal_faktur DESC");
        return $this->db->resultSet();
    }
    
    public function getPenjualanByIdWithDetails($id) {
        $this->db->query("SELECT p.*, pl.nama_pelanggan, pl.alamat as alamat_pelanggan
                         FROM penjualan p
                         LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                         WHERE p.id_penjualan = :id");
        $this->db->bind('id', $id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT pd.*, a.nama_akun, ms.kode_barang, ms.nama_barang, ms.id_barang, pd.kuantitas
                         FROM penjualan_detail pd
                         JOIN master_persediaan ms ON pd.id_barang = ms.id_barang
                         JOIN akun a ON ms.akun_penjualan = a.kode_akun
                         WHERE pd.id_penjualan = :id");
        $this->db->bind('id', $id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }
    
    public function simpanPenjualan($data) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);
        
        $this->db->beginTransaction();
        try {
            // Ambil akun piutang dari pengaturan
            $this->db->query("SELECT akun_piutang_default FROM perusahaan WHERE id = 1");
            $akun_piutang_usaha = $this->db->single()['akun_piutang_default'];
            if (empty($akun_piutang_usaha)) {
                throw new Exception("Akun Piutang Usaha default belum diatur di Pengaturan Perusahaan.");
            }

            // Validasi Stok
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang);
                if (!$barang) throw new Exception("Data barang dengan ID {$id_barang} tidak ditemukan.");
                $qty_dijual = (float)$data['details']['kuantitas'][$index];
                if ($barang['stok_saat_ini'] < $qty_dijual) {
                    throw new Exception("Stok untuk '{$barang['nama_barang']}' tidak cukup (tersisa: {$barang['stok_saat_ini']}).");
                }
            }

            // Siapkan SATU jurnal komprehensif
            $totalPenjualan = array_sum($data['details']['subtotal']);
            $jurnalData = [
                'no_transaksi' => $data['no_faktur'],
                'tanggal' => $data['tanggal_faktur'],
                'deskripsi' => 'Penjualan kpd ' . $data['nama_pelanggan'] . ' (Faktur #' . $data['no_faktur'] . ')',
                'sumber_jurnal' => 'Penjualan',
                'details' => []
            ];

            if ($data['metode_pembayaran'] === 'Tunai') {
                $jurnalData['details'][] = ['kode_akun' => $data['akun_kas_bank'], 'debit' => $totalPenjualan, 'kredit' => 0];
            } else {
                $jurnalData['details'][] = ['kode_akun' => $akun_piutang_usaha, 'debit' => $totalPenjualan, 'kredit' => 0];
            }

            $pendapatanGrouped = []; $hppGroupedDebit = []; $hppGroupedKredit = [];
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang);
                $subtotal_hpp = (float)$data['details']['kuantitas'][$index] * (float)$barang['harga_beli'];
                $pendapatanGrouped[$barang['akun_penjualan']] = ($pendapatanGrouped[$barang['akun_penjualan']] ?? 0) + $data['details']['subtotal'][$index];
                $hppGroupedDebit[$barang['akun_hpp']] = ($hppGroupedDebit[$barang['akun_hpp']] ?? 0) + $subtotal_hpp;
                $hppGroupedKredit[$barang['akun_persediaan']] = ($hppGroupedKredit[$barang['akun_persediaan']] ?? 0) + $subtotal_hpp;
            }
            foreach ($pendapatanGrouped as $akun => $total) { $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => 0, 'kredit' => $total]; }
            foreach ($hppGroupedDebit as $akun => $total) { $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => $total, 'kredit' => 0]; }
            foreach ($hppGroupedKredit as $akun => $total) { $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => 0, 'kredit' => $total]; }
            
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal penjualan.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();
            
            $sisa_tagihan = ($data['metode_pembayaran'] === 'Kredit') ? $totalPenjualan : 0.00;
            $status_pembayaran = ($data['metode_pembayaran'] === 'Kredit') ? 'Belum Lunas' : 'Lunas';

            $queryPenjualan = "INSERT INTO penjualan (id_pelanggan, id_jurnal, no_faktur, tanggal_faktur, total, keterangan, metode_pembayaran, akun_kas_bank, sisa_tagihan, status_pembayaran) 
                               VALUES (:id_pelanggan, :id_jurnal, :no_faktur, :tanggal, :total, :keterangan, :metode, :akun_kas, :sisa_tagihan, :status)";
            $this->db->query($queryPenjualan);
            $this->db->bind('id_pelanggan', $data['id_pelanggan']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_faktur', $data['no_faktur']);
            $this->db->bind('tanggal', $data['tanggal_faktur']);
            $this->db->bind('total', $totalPenjualan);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('metode', $data['metode_pembayaran']);
            $this->db->bind('akun_kas', ($data['metode_pembayaran'] === 'Tunai') ? $data['akun_kas_bank'] : null);
            $this->db->bind('sisa_tagihan', $sisa_tagihan);
            $this->db->bind('status', $status_pembayaran);
            $this->db->execute();
            $id_penjualan = $this->db->lastInsertId();

            $queryDetail = "INSERT INTO penjualan_detail (id_penjualan, id_barang, kuantitas, harga, subtotal, akun_pendapatan) VALUES (:id_penjualan, :id_barang, :qty, :harga, :subtotal, :akun)";
            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini - :qty WHERE id_barang = :id_barang";
            $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'OUT', :qty, :keterangan)";
            
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang);
                if (!$barang) throw new Exception("Integritas data terganggu: Barang dengan ID {$id_barang} tidak ditemukan saat proses detail.");
                $qty = $data['details']['kuantitas'][$index];
                
                $this->db->query($queryDetail);
                $this->db->bind('id_penjualan', $id_penjualan);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('harga', $data['details']['harga'][$index]);
                $this->db->bind('subtotal', $data['details']['subtotal'][$index]);
                $this->db->bind('akun', $barang['akun_penjualan']);
                $this->db->execute();
                
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', $qty);
                $this->db->bind('id_barang', $id_barang);
                $this->db->execute();

                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('keterangan', 'Faktur Penjualan: ' . $data['no_faktur']);
                $this->db->execute();
            }

            if ($data['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pelanggan SET saldo_terkini_piutang = saldo_terkini_piutang + :total WHERE id_pelanggan = :id");
                $this->db->bind('total', $totalPenjualan);
                $this->db->bind('id', $data['id_pelanggan']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    public function hapusPenjualan($id_penjualan) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);

        $this->db->beginTransaction();
        try {
            $penjualan = $this->getPenjualanByIdWithDetails($id_penjualan);
            if (!$penjualan) {
                throw new Exception('Faktur penjualan tidak ditemukan.');
            }

            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini + :qty WHERE id_barang = :id_barang";
            $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'IN', :qty, :keterangan)";
            foreach ($penjualan['details'] as $item) {
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->execute();
                
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('keterangan', 'Reversal/Pembatalan Faktur Penjualan: ' . $penjualan['no_faktur']);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM penjualan WHERE id_penjualan = :id");
            $this->db->bind('id', $id_penjualan);
            $this->db->execute();

            if ($penjualan['id_jurnal']) {
                $jurnalModel->hapusJurnal($penjualan['id_jurnal'], true);
            }
            
            if ($penjualan['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pelanggan SET saldo_terkini_piutang = saldo_terkini_piutang - :total WHERE id_pelanggan = :id");
                $this->db->bind('total', (float)$penjualan['total']);
                $this->db->bind('id', $penjualan['id_pelanggan']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }
}

