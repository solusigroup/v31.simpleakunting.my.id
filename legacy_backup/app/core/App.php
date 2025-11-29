<?php

/*
|--------------------------------------------------------------------------
| Kelas App (Router Utama)
|--------------------------------------------------------------------------
|
| Kelas ini adalah inti dari aplikasi. Ia membaca URL, menerapkan aturan
| keamanan (memaksa login), lalu menentukan controller, method, dan
| parameter apa yang harus dijalankan.
|
*/

class App {
    protected $controller = 'Dashboard';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();

        // --- GERBANG KEAMANAN YANG DIPERBARUI ---

        // Cek apakah controller yang dituju adalah 'login'.
        $isLoginController = (!empty($url) && strtolower($url[0]) === 'login');
        
        // Cek apakah method yang dituju adalah 'index' (artinya, form login).
        // Jika method tidak ada, defaultnya adalah 'index'.
        $isLoginForm = ($isLoginController && (!isset($url[1]) || strtolower($url[1]) === 'index'));

        // Skenario 1: Pengguna BELUM login DAN TIDAK sedang mencoba mengakses controller login.
        if (!Auth::isLoggedIn() && !$isLoginController) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        // Skenario 2: Pengguna SUDAH login TETAPI mencoba mengakses FORM login.
        if (Auth::isLoggedIn() && $isLoginForm) {
            // Ini tidak perlu, arahkan mereka ke halaman utama.
             header('Location: ' . BASEURL . '/dashboard');
             exit;
        }
        // --- AKHIR GERBANG KEAMANAN ---


        // --- Menentukan Controller ---
        if (!empty($url) && file_exists(APPROOT . '/app/controllers/' . ucfirst($url[0]) . '.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        require_once APPROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // --- Menentukan Method ---
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // --- Menentukan Parameter ---
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // --- Jalankan Semuanya! ---
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}