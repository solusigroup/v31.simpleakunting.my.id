<?php

class Akun_model {
    private $table = 'akun';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllAkun()
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY kode_akun ASC');
        return $this->db->resultSet();
    }

    public function tambahDataAkun($data)
    {
        // Keamanan: Saldo awal untuk Header selalu 0
        $saldo_awal = ($data['tipe_akun'] == 'Header') ? 0.00 : ($data['saldo_awal'] ?? 0.00);

        $query = "INSERT INTO {$this->table} (kode_akun, nama_akun, tipe_akun, posisi_saldo_normal, saldo_awal) 
                  VALUES (:kode_akun, :nama_akun, :tipe_akun, :posisi_saldo_normal, :saldo_awal)";
        
        $this->db->query($query);
        $this->db->bind('kode_akun', $data['kode_akun']);
        $this->db->bind('nama_akun', $data['nama_akun']);
        $this->db->bind('tipe_akun', $data['tipe_akun']);
        $this->db->bind('posisi_saldo_normal', $data['posisi_saldo_normal']);
        $this->db->bind('saldo_awal', $saldo_awal);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * FUNGSI YANG DIPERBARUI dengan logika keamanan untuk Saldo Awal.
     */
    public function ubahDataAkun($data)
    {
        // **LOGIKA BARU: Pastikan saldo awal untuk Header selalu 0 di sisi server**
        $saldo_awal = ($data['tipe_akun'] == 'Header') ? 0.00 : ($data['saldo_awal'] ?? 0.00);

        $query = "UPDATE {$this->table} SET 
                    nama_akun = :nama_akun,
                    tipe_akun = :tipe_akun,
                    posisi_saldo_normal = :posisi_saldo_normal,
                    saldo_awal = :saldo_awal
                  WHERE kode_akun = :kode_akun";
        
        $this->db->query($query);
        $this->db->bind('nama_akun', $data['nama_akun']);
        $this->db->bind('tipe_akun', $data['tipe_akun']);
        $this->db->bind('posisi_saldo_normal', $data['posisi_saldo_normal']);
        $this->db->bind('saldo_awal', $saldo_awal); // Menggunakan variabel yang sudah divalidasi
        $this->db->bind('kode_akun', $data['kode_akun']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function importFromExcel($data)
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0; DELETE FROM ' . $this->table . '; SET FOREIGN_KEY_CHECKS=1;');
        $this->db->execute();

        $rowCount = 0;
        foreach ($data as $akun) {
            if (!empty($akun['kode_akun'])) {
                $this->tambahDataAkun($akun);
                $rowCount++;
            }
        }
        return $rowCount;
    }

    public function hapusDataAkun($kode)
    {
        $query = "DELETE FROM {$this->table} WHERE kode_akun = :kode_akun";
        $this->db->query($query);
        $this->db->bind('kode_akun', $kode);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function getAkunByKode($kode)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE kode_akun=:kode_akun');
        $this->db->bind('kode_akun', $kode);
        return $this->db->single();
    }
    
    public function isKodeAkunExists($kode)
    {
        $this->db->query('SELECT 1 FROM ' . $this->table . ' WHERE kode_akun = :kode_akun');
        $this->db->bind('kode_akun', $kode);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}