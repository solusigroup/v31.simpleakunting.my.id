<?php

class Login extends Controller {
    /**
     * Constructor ini sangat penting.
     * Ia memastikan koneksi database ($this->db) dibuat dengan memanggil
     * constructor dari Controller induk sebelum method lain di kelas ini dijalankan.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Menampilkan halaman login.
     * Jika pengguna sudah login, ia akan diarahkan ke dashboard.
     */
    public function index() {
        if (Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }
        $data['judul'] = 'Login';
        $this->view('login/login', $data);
    }

    /**
     * Memproses data yang dikirim dari form login.
     */
    public function process() {
        $nama_user = $_POST['nama_user'];
        $password = $_POST['password'];

        // Panggil model untuk mencari pengguna berdasarkan nama
        $user = $this->model('User')->getUserByUsername($nama_user);

        // Verifikasi: Apakah pengguna ditemukan DAN passwordnya cocok?
        if ($user && password_verify($password, $user['password_hash'])) {
            // Jika berhasil, atur sesi dan arahkan ke dashboard
            Auth::setUser($user);
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        } else {
            // Jika gagal, kembali ke halaman login dengan pesan error
            Flash::setFlash('Nama pengguna atau sandi salah.', 'danger');
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    /**
     * Menangani proses logout.
     */
    public function logout() {
        Auth::logout();
        header('Location: ' . BASEURL . '/login');
        exit;
    }
}

