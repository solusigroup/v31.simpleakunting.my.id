<?php

class Flash {
    /**
     * Mengatur session flash message.
     * @param string $pesan Pesan yang akan ditampilkan.
     * @param string $tipe Tipe notifikasi (Bootstrap: success, danger, warning, info).
     */
    public static function setFlash($pesan, $tipe)
    {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'tipe'  => $tipe
        ];
    }

    /**
     * Menampilkan flash message jika ada dan langsung menghapusnya.
     */
    public static function flash()
    {
        if (isset($_SESSION['flash'])) {
            echo '<div class="alert alert-' . $_SESSION['flash']['tipe'] . ' alert-dismissible fade show" role="alert">
                    ' . $_SESSION['flash']['pesan'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            // Hapus session setelah ditampilkan
            unset($_SESSION['flash']);
        }
    }
}

