<?php

class Controller {
    /**
     * Metode untuk memuat dan menampilkan file view.
     * @param string $view Path ke file view dari folder 'views'.
     * @param array $data Data yang akan diekstrak menjadi variabel di dalam view.
     */
    public function view($view, $data = [])
    {
        // Cek apakah file view-nya ada
        if (file_exists(APPROOT . '/app/views/' . $view . '.php')) {
            require_once APPROOT . '/app/views/' . $view . '.php';
        } else {
            // Jika tidak ada, hentikan aplikasi dan beri pesan error yang jelas
            die('View tidak ditemukan di: ' . APPROOT . '/app/views/' . $view . '.php');
        }
    }

    /**
     * Metode untuk memuat file model.
     * @param string $model Nama file model (tanpa _model.php).
     * @return object Instance dari model yang diminta.
     */
    public function model($model)
    {
        // Muat file model
        require_once APPROOT . '/app/models/' . $model . '_model.php';
        
        // Buat instance dari kelas modelnya
        $modelName = $model . '_model';
        return new $modelName();
    }
}

