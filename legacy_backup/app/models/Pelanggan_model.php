<?php

class Pelanggan_model {
    private $table = 'pelanggan';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPelanggan() {
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY nama_pelanggan ASC');
        return $this->db->resultSet();
    }

    public function getPelangganById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_pelanggan=:id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahDataPelanggan($data) {
        $query = "INSERT INTO {$this->table} (nama_pelanggan, alamat, telepon, email, saldo_awal_piutang, saldo_terkini_piutang) 
                  VALUES (:nama, :alamat, :telepon, :email, :saldo_awal, :saldo_terkini)";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_pelanggan']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_piutang']);
        $this->db->bind('saldo_terkini', $data['saldo_awal_piutang']); // Saldo terkini diinisialisasi sama dengan saldo awal
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataPelanggan($id) {
        $query = "DELETE FROM {$this->table} WHERE id_pelanggan = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataPelanggan($data) {
        $query = "UPDATE {$this->table} SET 
                    nama_pelanggan = :nama,
                    alamat = :alamat,
                    telepon = :telepon,
                    email = :email,
                    saldo_awal_piutang = :saldo_awal
                    -- Saldo terkini tidak diubah dari sini
                  WHERE id_pelanggan = :id";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_pelanggan']);
        $this->db->bind('alamat', $data['alamat']);
        $this->db->bind('telepon', $data['telepon']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('saldo_awal', $data['saldo_awal_piutang']);
        $this->db->bind('id', $data['id_pelanggan']);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

