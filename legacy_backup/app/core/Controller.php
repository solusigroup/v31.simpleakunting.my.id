<?php

class Controller {
    /**
     * @var Database Properti untuk menyimpan satu instance koneksi database.
     */
    protected $db;

    /**
     * Constructor ini berjalan secara otomatis untuk setiap controller.
     * Ia membuat satu koneksi database yang akan digunakan bersama.
     */
    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Metode untuk memuat dan menampilkan file view.
     * @param string $view Path ke file view dari folder 'views'.
     * @param array $data Data yang akan diekstrak menjadi variabel di dalam view.
     */
    public function view($view, $data = [])
    {
        $viewFile = APPROOT . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('Error: File view tidak ditemukan di: ' . $viewFile);
        }
    }

    /**
     * Metode untuk memuat file model dan menyuntikkan koneksi database.
     * @param string $model Nama file model (tanpa _model.php).
     * @return object Instance dari model yang diminta, yang sudah memiliki koneksi database.
     */
    public function model($model)
    {
        // Muat file model
        require_once APPROOT . '/app/models/' . $model . '_model.php';
        
        // Buat instance dari kelas modelnya
        $modelName = $model . '_model';
        // Berikan koneksi database ($this->db) ke constructor model
        return new $modelName($this->db);
    }
}

