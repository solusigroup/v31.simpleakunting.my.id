<?php

// Muat semua model yang dibutuhkan secara manual
require_once 'Jurnal_model.php';
require_once 'Persediaan_model.php';

class Pembelian_model {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPembelian() {
        $this->db->query("SELECT p.*, pm.nama_pemasok 
                         FROM pembelian p
                         LEFT JOIN pemasok pm ON p.id_pemasok = pm.id_pemasok
                         ORDER BY p.tanggal_faktur DESC");
        return $this->db->resultSet();
    }
    
    public function getPembelianByIdWithDetails($id) {
        $this->db->query("SELECT p.*, pm.nama_pemasok, pm.alamat as alamat_pemasok
                         FROM pembelian p
                         LEFT JOIN pemasok pm ON p.id_pemasok = pm.id_pemasok
                         WHERE p.id_pembelian = :id");
        $this->db->bind('id', $id);
        $header = $this->db->single();
        if (!$header) return false;

        $this->db->query("SELECT pd.*, ms.id_barang, ms.kode_barang, ms.nama_barang, pd.kuantitas
                         FROM pembelian_detail pd
                         JOIN master_persediaan ms ON pd.id_barang = ms.id_barang
                         WHERE pd.id_pembelian = :id");
        $this->db->bind('id', $id);
        $header['details'] = $this->db->resultSet();
        return $header;
    }
    
    public function simpanPembelian($data) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $this->db->query("SELECT akun_utang_default FROM perusahaan WHERE id = 1");
            $akun_utang_usaha = $this->db->single()['akun_utang_default'];
            if (empty($akun_utang_usaha)) throw new Exception("Akun Utang Usaha default belum diatur.");

            $totalPembelian = array_sum($data['details']['subtotal']);
            $jurnalData = [
                'no_transaksi' => $data['no_faktur_pembelian'],
                'tanggal' => $data['tanggal_faktur'],
                'deskripsi' => 'Pembelian dari ' . $data['nama_pemasok'],
                'sumber_jurnal' => 'Pembelian',
                'details' => []
            ];

            if ($data['metode_pembayaran'] === 'Tunai') {
                $jurnalData['details'][] = ['kode_akun' => $data['akun_kas_bank'], 'debit' => 0, 'kredit' => $totalPembelian];
            } else {
                $jurnalData['details'][] = ['kode_akun' => $akun_utang_usaha, 'debit' => 0, 'kredit' => $totalPembelian];
            }

            $bebanGrouped = [];
            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $barang = $persediaanModel->getBarangById($id_barang);
                if (!$barang) { throw new Exception("Data barang tidak ditemukan."); }
                $akun_beban_persediaan = $barang['akun_persediaan'];
                $subtotal = $data['details']['subtotal'][$index];
                $bebanGrouped[$akun_beban_persediaan] = ($bebanGrouped[$akun_beban_persediaan] ?? 0) + $subtotal;
            }
            foreach ($bebanGrouped as $akun => $subtotal) {
                $jurnalData['details'][] = ['kode_akun' => $akun, 'debit' => $subtotal, 'kredit' => 0];
            }
            
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan entri jurnal.");
            
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            $sisa_tagihan = ($data['metode_pembayaran'] === 'Kredit') ? $totalPembelian : 0.00;
            $status_pembayaran = ($data['metode_pembayaran'] === 'Kredit') ? 'Belum Lunas' : 'Lunas';

            $queryPembelian = "INSERT INTO pembelian (id_pemasok, id_jurnal, no_faktur_pembelian, tanggal_faktur, total, keterangan, metode_pembayaran, akun_kas_bank, sisa_tagihan, status_pembayaran)
                               VALUES (:id_pemasok, :id_jurnal, :no_faktur, :tanggal, :total, :keterangan, :metode, :akun_kas, :sisa_tagihan, :status)";
            $this->db->query($queryPembelian);
            $this->db->bind('id_pemasok', $data['id_pemasok']);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('no_faktur', $data['no_faktur_pembelian']);
            $this->db->bind('tanggal', $data['tanggal_faktur']);
            $this->db->bind('total', $totalPembelian);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('metode', $data['metode_pembayaran']);
            $this->db->bind('akun_kas', ($data['metode_pembayaran'] === 'Tunai') ? $data['akun_kas_bank'] : null);
            $this->db->bind('sisa_tagihan', $sisa_tagihan);
            $this->db->bind('status', $status_pembayaran);
            $this->db->execute();
            $id_pembelian = $this->db->lastInsertId();

            // **PERBAIKAN: Query INSERT sekarang menyertakan `akun_beban_persediaan`**
            $queryDetail = "INSERT INTO pembelian_detail (id_pembelian, id_barang, kuantitas, harga, subtotal, akun_beban_persediaan)
                            VALUES (:id_pembelian, :id_barang, :qty, :harga, :subtotal, :akun)";
            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini + :qty WHERE id_barang = :id_barang";
            $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'IN', :qty, :keterangan)";

            foreach ($data['details']['id_barang'] as $index => $id_barang) {
                $qty = $data['details']['kuantitas'][$index];
                $barang = $persediaanModel->getBarangById($id_barang);
                if (!$barang) throw new Exception("Integritas data terganggu.");

                $this->db->query($queryDetail);
                $this->db->bind('id_pembelian', $id_pembelian);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('harga', $data['details']['harga'][$index]);
                $this->db->bind('subtotal', $data['details']['subtotal'][$index]);
                $this->db->bind('akun', $barang['akun_persediaan']); // Menggunakan akun dari master barang
                $this->db->execute();
                
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', $qty);
                $this->db->bind('id_barang', $id_barang);
                $this->db->execute();

                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $qty);
                $this->db->bind('keterangan', 'Faktur Pembelian: ' . $data['no_faktur_pembelian']);
                $this->db->execute();
            }

            if ($data['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang + :total WHERE id_pemasok = :id");
                $this->db->bind('total', $totalPembelian);
                $this->db->bind('id', $data['id_pemasok']);
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
    
    public function hapusPembelian($id_pembelian) {
        $jurnalModel = new Jurnal_model($this->db);
        $persediaanModel = new Persediaan_model($this->db);

        $this->db->beginTransaction();
        try {
            $pembelian = $this->getPembelianByIdWithDetails($id_pembelian);
            if (!$pembelian) throw new Exception('Faktur pembelian tidak ditemukan.');

            $queryUpdateStok = "UPDATE master_persediaan SET stok_saat_ini = stok_saat_ini - :qty WHERE id_barang = :id_barang";
            $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'OUT', :qty, :keterangan)";
            foreach ($pembelian['details'] as $item) {
                $this->db->query($queryUpdateStok);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->execute();
                
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $item['id_barang']);
                $this->db->bind('qty', (float)$item['kuantitas']);
                $this->db->bind('keterangan', 'Reversal Faktur Pembelian: ' . $pembelian['no_faktur_pembelian']);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM pembelian WHERE id_pembelian = :id");
            $this->db->bind('id', $id_pembelian);
            $this->db->execute();

            if ($pembelian['id_jurnal']) {
                $jurnalModel->hapusJurnal($pembelian['id_jurnal'], true);
            }
            
            if ($pembelian['metode_pembayaran'] === 'Kredit') {
                $this->db->query("UPDATE pemasok SET saldo_terkini_hutang = saldo_terkini_hutang - :total WHERE id_pemasok = :id");
                $this->db->bind('total', (float)$pembelian['total']);
                $this->db->bind('id', $pembelian['id_pemasok']);
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

