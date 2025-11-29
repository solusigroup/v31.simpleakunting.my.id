<?php

class Persediaan_model {
    private $table = 'master_persediaan';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBarang() {
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY nama_barang ASC');
        return $this->db->resultSet();
    }
    
    public function getBarangById($id_barang) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_barang = :id');
        $this->db->bind('id', $id_barang);
        return $this->db->single();
    }
    
    public function isKodeBarangExists($kode_barang) {
        $this->db->query('SELECT 1 FROM ' . $this->table . ' WHERE kode_barang = :kode');
        $this->db->bind('kode', $kode_barang);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function tambahDataBarang($data) {
        $query = "INSERT INTO {$this->table} 
                    (kode_barang, nama_barang, satuan, stok_awal, stok_saat_ini, harga_beli, harga_jual, akun_persediaan, akun_hpp, akun_penjualan) 
                  VALUES 
                    (:kode, :nama, :satuan, :stok_awal, :stok_saat_ini, :harga_beli, :harga_jual, :akun_persediaan, :akun_hpp, :akun_penjualan)";
        
        $this->db->beginTransaction();
        try {
            $this->db->query($query);
            $this->db->bind('kode', $data['kode_barang']);
            $this->db->bind('nama', $data['nama_barang']);
            $this->db->bind('satuan', $data['satuan']);
            $this->db->bind('stok_awal', $data['stok_awal']);
            $this->db->bind('stok_saat_ini', $data['stok_awal']);
            $this->db->bind('harga_beli', $data['harga_beli']);
            $this->db->bind('harga_jual', $data['harga_jual']);
            $this->db->bind('akun_persediaan', $data['akun_persediaan']);
            $this->db->bind('akun_hpp', $data['akun_hpp']);
            $this->db->bind('akun_penjualan', $data['akun_penjualan']);
            $this->db->execute();
            $id_barang = $this->db->lastInsertId();

            if ((float)$data['stok_awal'] > 0) {
                $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, 'IN', :qty, :keterangan)";
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $id_barang);
                $this->db->bind('qty', $data['stok_awal']);
                $this->db->bind('keterangan', 'Stok Awal');
                $this->db->execute();
            }
            
            $this->db->commit();
            return $this->db->rowCount();
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return 0;
        }
    }

    public function ubahDataBarang($data) {
        $this->db->beginTransaction();
        try {
            $barangLama = $this->getBarangById($data['id_barang']);
            if (!$barangLama) {
                throw new Exception("Barang tidak ditemukan.");
            }

            $stokAwalLama = (float)$barangLama['stok_awal'];
            $stokAwalBaru = (float)$data['stok_awal'];
            $selisihStokAwal = $stokAwalBaru - $stokAwalLama;
            $stokTerkiniBaru = (float)$barangLama['stok_saat_ini'] + $selisihStokAwal;

            $query = "UPDATE {$this->table} SET 
                        kode_barang = :kode,
                        nama_barang = :nama,
                        satuan = :satuan,
                        stok_awal = :stok_awal,
                        stok_saat_ini = :stok_terkini,
                        harga_beli = :harga_beli,
                        harga_jual = :harga_jual,
                        akun_persediaan = :akun_persediaan,
                        akun_hpp = :akun_hpp,
                        akun_penjualan = :akun_penjualan
                      WHERE id_barang = :id";
            $this->db->query($query);
            $this->db->bind('kode', $data['kode_barang']);
            $this->db->bind('nama', $data['nama_barang']);
            $this->db->bind('satuan', $data['satuan']);
            $this->db->bind('stok_awal', $stokAwalBaru);
            $this->db->bind('stok_terkini', $stokTerkiniBaru);
            $this->db->bind('harga_beli', $data['harga_beli']);
            $this->db->bind('harga_jual', $data['harga_jual']);
            $this->db->bind('akun_persediaan', $data['akun_persediaan']);
            $this->db->bind('akun_hpp', $data['akun_hpp']);
            $this->db->bind('akun_penjualan', $data['akun_penjualan']);
            $this->db->bind('id', $data['id_barang']);
            $this->db->execute();
            $rowCount = $this->db->rowCount();

            if ($selisihStokAwal != 0) {
                $queryKartuStok = "INSERT INTO kartu_stok (id_barang, tipe_transaksi, kuantitas, keterangan) VALUES (:id_barang, :tipe, :qty, :keterangan)";
                $this->db->query($queryKartuStok);
                $this->db->bind('id_barang', $data['id_barang']);
                $this->db->bind('tipe', ($selisihStokAwal > 0) ? 'IN' : 'OUT');
                $this->db->bind('qty', abs($selisihStokAwal));
                $this->db->bind('keterangan', 'Penyesuaian Stok Awal');
                $this->db->execute();
            }

            $this->db->commit();
            return $rowCount;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            return 0;
        }
    }

    public function hapusDataBarang($id) {
        $query = "DELETE FROM {$this->table} WHERE id_barang = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    
    public function importFromExcel($data) {
        // Karena import adalah operasi besar, kita pastikan foreign key checks aman
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        $this->db->execute();
        $this->db->query('DELETE FROM ' . $this->table);
        $this->db->execute();

        $rowCount = 0;
        foreach ($data as $barang) {
            if (!empty($barang['kode_barang'])) {
                $this->tambahDataBarang($barang);
                $rowCount++;
            }
        }
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
        $this->db->execute();
        return $rowCount;
    }
}