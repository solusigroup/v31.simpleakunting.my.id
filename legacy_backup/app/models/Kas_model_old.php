<?php

require_once 'Jurnal_model.php';

class Kas_model {
    private $table = 'kas_transaksi';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTransaksi() {
        $this->db->query("SELECT kt.*, ak.nama_akun as nama_akun_kas, al.nama_akun as nama_akun_lawan
                         FROM {$this->table} kt
                         JOIN akun ak ON kt.akun_kas_bank = ak.kode_akun
                         JOIN akun al ON kt.akun_lawan = al.kode_akun
                         ORDER BY kt.tanggal DESC, kt.id_transaksi DESC");
        return $this->db->resultSet();
    }

    public function getTransaksiById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_transaksi = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function simpanTransaksi($data) {
        $jurnalModel = new Jurnal_model($this->db);
        
        $this->db->beginTransaction();
        try {
            $debit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_kas_bank'] : $data['akun_lawan'];
            $kredit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_lawan'] : $data['akun_kas_bank'];
            
            $jurnalData = [
                'no_transaksi' => $data['no_bukti'],
                'tanggal' => $data['tanggal'],
                'deskripsi' => $data['deskripsi'],
                'sumber_jurnal' => 'Kas & Bank',
                'details' => [
                    ['kode_akun' => $debit_akun, 'debit' => $data['jumlah'], 'kredit' => 0],
                    ['kode_akun' => $kredit_akun, 'debit' => 0, 'kredit' => $data['jumlah']]
                ]
            ];
            $id_jurnal = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal == 0) throw new Exception("Gagal menyimpan jurnal transaksi kas.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal);
            $this->db->execute();

            $query = "INSERT INTO {$this->table} (id_jurnal, tipe_transaksi, tanggal, no_bukti, akun_kas_bank, akun_lawan, jumlah, deskripsi) 
                      VALUES (:id_jurnal, :tipe, :tgl, :no_bukti, :akun_kas, :akun_lawan, :jumlah, :deskripsi)";
            $this->db->query($query);
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('tipe', $data['tipe_transaksi']);
            $this->db->bind('tgl', $data['tanggal']);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('akun_lawan', $data['akun_lawan']);
            $this->db->bind('jumlah', $data['jumlah']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    /**
     * Mengupdate transaksi kas yang ada.
     * Logika cerdasnya: hapus jurnal lama, buat jurnal baru, lalu update datanya.
     */
    public function updateTransaksi($data) {
        $jurnalModel = new Jurnal_model($this->db);
        $this->db->beginTransaction();
        try {
            // 1. Hapus jurnal lama terlebih dahulu
            $transaksiLama = $this->getTransaksiById($data['id_transaksi']);
            if ($transaksiLama && $transaksiLama['id_jurnal']) {
                $jurnalModel->hapusJurnal($transaksiLama['id_jurnal'], true); // Hapus paksa
            }

            // 2. Buat jurnal baru dengan data yang diperbarui
            $debit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_kas_bank'] : $data['akun_lawan'];
            $kredit_akun = ($data['tipe_transaksi'] == 'Masuk') ? $data['akun_lawan'] : $data['akun_kas_bank'];
            $jurnalData = [
                'no_transaksi' => $data['no_bukti'],
                'tanggal' => $data['tanggal'],
                'deskripsi' => $data['deskripsi'],
                'sumber_jurnal' => 'Kas & Bank',
                'details' => [
                    ['kode_akun' => $debit_akun, 'debit' => $data['jumlah'], 'kredit' => 0],
                    ['kode_akun' => $kredit_akun, 'debit' => 0, 'kredit' => $data['jumlah']]
                ]
            ];
            $id_jurnal_baru = $jurnalModel->simpanJurnal($jurnalData);
            if ($id_jurnal_baru == 0) throw new Exception("Gagal membuat jurnal baru saat update.");

            $this->db->query("UPDATE jurnal_umum SET is_locked = 1 WHERE id_jurnal = :id");
            $this->db->bind('id', $id_jurnal_baru);
            $this->db->execute();
            
            // 3. Update data transaksi kas itu sendiri
            $query = "UPDATE {$this->table} SET 
                        id_jurnal = :id_jurnal, tipe_transaksi = :tipe, tanggal = :tgl, 
                        no_bukti = :no_bukti, akun_kas_bank = :akun_kas, akun_lawan = :akun_lawan, 
                        jumlah = :jumlah, deskripsi = :deskripsi
                      WHERE id_transaksi = :id_transaksi";
            $this->db->query($query);
            $this->db->bind('id_jurnal', $id_jurnal_baru);
            $this->db->bind('tipe', $data['tipe_transaksi']);
            $this->db->bind('tgl', $data['tanggal']);
            $this->db->bind('no_bukti', $data['no_bukti']);
            $this->db->bind('akun_kas', $data['akun_kas_bank']);
            $this->db->bind('akun_lawan', $data['akun_lawan']);
            $this->db->bind('jumlah', $data['jumlah']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('id_transaksi', $data['id_transaksi']);
            $this->db->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            Flash::setFlash($e->getMessage(), 'danger');
            return false;
        }
    }

    /**
     * Menghapus transaksi kas dan jurnal terkaitnya.
     */
    public function hapusTransaksi($id) {
        $this->db->beginTransaction();
        try {
            $transaksi = $this->getTransaksiById($id);
            if (!$transaksi) {
                throw new Exception('Transaksi kas tidak ditemukan.');
            }

            // Hapus transaksi kas
            $this->db->query("DELETE FROM {$this->table} WHERE id_transaksi = :id");
            $this->db->bind('id', $id);
            $this->db->execute();

            // Hapus paksa jurnal yang terkait
            if ($transaksi['id_jurnal']) {
                $jurnalModel = new Jurnal_model($this->db);
                $jurnalModel->hapusJurnal($transaksi['id_jurnal'], true);
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

