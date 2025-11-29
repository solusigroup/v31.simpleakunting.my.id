<?php

class Perusahaan_model {
    private $table = 'perusahaan';
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPerusahaan() {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = 1');
        return $this->db->single();
    }

    public function updatePerusahaan($data, $logo_path) {
        $query = "UPDATE {$this->table} SET 
                    nama_perusahaan = :nama,
                    alamat = :alamat,
                    telepon = :telepon,
                    email = :email,
                    kota_laporan = :kota_laporan,
                    path_logo = :path_logo,
                    penandatangan_1_id = :p1_id,
                    penandatangan_2_id = :p2_id,
                    akun_piutang_default = :akun_piutang,
                    akun_utang_default = :akun_utang,
                    akun_laba_ditahan = :akun_laba_ditahan,
                    akun_ikhtisar_lr = :akun_ikhtisar_lr
                  WHERE id = 1";
                  
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_perusahaan']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('kota_laporan', $data['kota_laporan']);
        $this->db->bind('path_logo', $logo_path);
        $this->db->bind('p1_id', $data['penandatangan_1_id']);
        $this->db->bind('p2_id', $data['penandatangan_2_id']);
        $this->db->bind('akun_piutang', $data['akun_piutang_default']);
        $this->db->bind('akun_utang', $data['akun_utang_default']);
        $this->db->bind('akun_laba_ditahan', $data['akun_laba_ditahan']);
        $this->db->bind('akun_ikhtisar_lr', $data['akun_ikhtisar_lr']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }
}

