<?php

class User_model {
    private $table = 'users';
    private $db;

    /**
     * Constructor baru yang menerima koneksi database dari Controller.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY nama_user ASC');
        return $this->db->resultSet();
    }

    public function getUserById($id) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_user=:id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }
    
    public function getUserByUsername($nama_user) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE nama_user = :nama_user');
        $this->db->bind('nama_user', $nama_user);
        return $this->db->single();
    }

    public function tambahDataUser($data) {
        $query = "INSERT INTO {$this->table} (nama_user, password_hash, role, jabatan) 
                  VALUES (:nama, :password, :role, :jabatan)";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_user']);
        $this->db->bind('password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind('role', $data['role']);
        $this->db->bind('jabatan', $data['jabatan']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataUser($id) {
        $query = "DELETE FROM {$this->table} WHERE id_user = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function ubahDataUser($data) {
        if (!empty($data['password'])) {
            $query = "UPDATE {$this->table} SET 
                        nama_user = :nama,
                        password_hash = :password,
                        role = :role,
                        jabatan = :jabatan
                      WHERE id_user = :id";
            $this->db->query($query);
            $this->db->bind('password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $query = "UPDATE {$this->table} SET 
                        nama_user = :nama,
                        role = :role,
                        jabatan = :jabatan
                      WHERE id_user = :id";
            $this->db->query($query);
        }
        
        $this->db->bind('nama', $data['nama_user']);
        $this->db->bind('role', $data['role']);
        $this->db->bind('jabatan', $data['jabatan']);
        $this->db->bind('id', $data['id_user']);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
