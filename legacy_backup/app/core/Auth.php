<?php

class Auth {
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menyimpan data pengguna ke dalam session setelah login berhasil.
     * Pastikan kita konsisten menggunakan 'user_name' sebagai kunci sesi.
     */
    public static function setUser($user) {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['nama_user']; // Kunci 'user_name' digunakan di sini
        $_SESSION['user_role'] = $user['role'];
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function logout() {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Mengambil data pengguna yang sedang login.
     * Pastikan kita membaca dari kunci 'user_name' yang sama.
     */
    public static function user() {
        self::startSession();
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'], // Kunci 'user_name' dibaca di sini
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }

    public static function hasRole($role) {
        return self::isLoggedIn() && self::user()['role'] === $role;
    }

    public static function isAdmin() {
        return self::hasRole('Admin');
    }

    public static function isManager() {
        return self::hasRole('Manager');
    }

    public static function isStaff() {
        return self::hasRole('Staff');
    }
}

