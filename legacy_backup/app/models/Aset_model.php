<?php
// **PERBAIKAN 1: Muat file Jurnal_model secara manual di sini**
require_once 'Jurnal_model.php';

class Aset_model {
    private $table = 'master_aset_tetap';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllAset() {
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY kode_aset ASC');
        return $this->db->resultSet();
    }
    
    public function getAsetById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_aset = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }
   
    public function tambahDataAset($data) {
        $query = "INSERT INTO {$this->table} (kode_aset, nama_aset, kelompok_aset, tanggal_perolehan, harga_perolehan, masa_manfaat, akun_aset, akun_akumulasi_depresiasi, akun_beban_depresiasi) 
                  VALUES (:kode, :nama, :kelompok, :tgl, :harga, :masa, :akun_aset, :akun_akumulasi, :akun_beban)";
        $this->db->query($query);
        $this->db->bind('kode', $data['kode_aset']);
        $this->db->bind('nama', $data['nama_aset']);
        $this->db->bind('kelompok', $data['kelompok_aset']);
        $this->db->bind('tgl', $data['tanggal_perolehan']);
        $this->db->bind('harga', $data['harga_perolehan']);
        $this->db->bind('masa', $data['masa_manfaat']);
        $this->db->bind('akun_aset', $data['akun_aset']);
        $this->db->bind('akun_akumulasi', $data['akun_akumulasi_depresiasi']);
        $this->db->bind('akun_beban', $data['akun_beban_depresiasi']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataAset($data) {
        $query = "UPDATE {$this->table} SET 
                    kode_aset = :kode, nama_aset = :nama, kelompok_aset = :kelompok, 
                    tanggal_perolehan = :tgl, harga_perolehan = :harga, masa_manfaat = :masa, 
                    akun_aset = :akun_aset, akun_akumulasi_depresiasi = :akun_akumulasi, akun_beban_depresiasi = :akun_beban
                  WHERE id_aset = :id";
        $this->db->query($query);
        $this->db->bind('kode', $data['kode_aset']);
        $this->db->bind('nama', $data['nama_aset']);
        $this->db->bind('kelompok', $data['kelompok_aset']);
        $this->db->bind('tgl', $data['tanggal_perolehan']);
        $this->db->bind('harga', $data['harga_perolehan']);
        $this->db->bind('masa', $data['masa_manfaat']);
        $this->db->bind('akun_aset', $data['akun_aset']);
        $this->db->bind('akun_akumulasi', $data['akun_akumulasi_depresiasi']);
        $this->db->bind('akun_beban', $data['akun_beban_depresiasi']);
        $this->db->bind('id', $data['id_aset']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataAset($id) {
        $query = "DELETE FROM {$this->table} WHERE id_aset = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    /**
     * FUNGSI BARU: Menghitung data penyusutan untuk periode tertentu.
     * @param string $periode Format 'YYYY-MM'.
     * @return array Data penyusutan yang sudah dikelompokkan per akun.
     */
    public function getDepreciationData($periode) {
        $akhir_bulan = date('Y-m-t', strtotime($periode . '-01'));

        $this->db->query("SELECT 
                            akun_beban_depresiasi, 
                            akun_akumulasi_depresiasi,
                            SUM(harga_perolehan / (masa_manfaat * 12)) as beban_bulanan
                         FROM {$this->table}
                         WHERE tanggal_perolehan <= :akhir_bulan
                         GROUP BY akun_beban_depresiasi, akun_akumulasi_depresiasi");
        $this->db->bind('akhir_bulan', $akhir_bulan);
        return $this->db->resultSet();
    }
    
    /**
     * FUNGSI BARU: Menjalankan proses penjurnalan penyusutan.
     * @param string $periode Format 'YYYY-MM'.
     * @param array $depreciationData Data dari getDepreciationData().
     * @return bool True jika sukses.
     */
    public function runDepreciationJournal($periode, $depreciationData) {
        $jurnalModel = new Jurnal_model($this->db);
        $tanggal_jurnal = date('Y-m-t', strtotime($periode . '-01'));

        $jurnalData = [
            'no_transaksi' => 'AJE-DEP-' . date('Ym', strtotime($periode . '-01')),
            'tanggal' => $tanggal_jurnal,
            'deskripsi' => 'Penyesuaian Beban Penyusutan Periode ' . date('F Y', strtotime($periode . '-01')),
            'sumber_jurnal' => 'Adjustment',
            'details' => []
        ];

        foreach ($depreciationData as $data) {
            $beban = (float)$data['beban_bulanan'];
            if ($beban > 0) {
                // Sisi Debit (Beban Depresiasi)
                $jurnalData['details'][] = ['kode_akun' => $data['akun_beban_depresiasi'], 'debit' => $beban, 'kredit' => 0];
                // Sisi Kredit (Akumulasi Depresiasi)
                $jurnalData['details'][] = ['kode_akun' => $data['akun_akumulasi_depresiasi'], 'debit' => 0, 'kredit' => $beban];
            }
        }
        
        // Simpan jurnal hanya jika ada detail yang akan dijurnal
        if (empty($jurnalData['details'])) {
            return true; // Dianggap sukses jika tidak ada yang perlu disusutkan
        }

        $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
        if ($id_jurnal > 0) {
            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();
            return true;
        }
        return false;
    }
}